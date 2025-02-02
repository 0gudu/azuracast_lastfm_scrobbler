<?php
$dbname = 'accesscontrol';
$host = 'localhost';
$dbuser = 'root'; 
$dbpass = 'nova_senha';

try {
    $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $dbuser, $dbpass, [
        PDO::MYSQL_ATTR_LOCAL_INFILE => true
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

// artista e musica
$json = file_get_contents('php://input');
$data = json_decode($json);

if ($data === null) {
    die("Erro ao decodificar JSON.");
}

if (!isset($data->now_playing->song->title) || !isset($data->now_playing->song->artist)) {
    die("Dados de música ou artista não encontrados.");
}

$song_title = $data->now_playing->song->title;
$artist_name = $data->now_playing->song->artist;

echo "Música: " . $song_title . "<br>Artista: " . $artist_name . "<br>";

// Função para verificar e atualizar o status
function checkAndUpdateStatus($id, $pdo) {
    $sql = "SELECT * FROM acessos WHERE uniqueidclient = :id";
    $comando = $pdo->prepare($sql);
    $comando->bindParam(':id', $id, PDO::PARAM_STR);
    $comando->execute();
    $linha = $comando->fetch(PDO::FETCH_ASSOC);

    if (!$linha) {
        die("Erro: Não foi possível encontrar o sessionKey.");
    }

    $tempo = $linha['char5'];
    $char5_timestamp = strtotime($tempo);
    if ($char5_timestamp === false) {
        die("Erro: Timestamp inválido em char5.");
    }

    $agora_timestamp = time();
    $diferenca = $agora_timestamp - $char5_timestamp;

    $status = ($diferenca > 30) ? 'offline' : 'online';

    $sql = "UPDATE acessos SET char6 = :status WHERE uniqueidclient = :id";
    $comando = $pdo->prepare($sql);
    $comando->bindParam(':id', $id, PDO::PARAM_STR);
    $comando->bindParam(':status', $status, PDO::PARAM_STR);
    $comando->execute();

    return $status;
}

// Função para realizar scrobble se o cliente estiver online
function scrobbleIfOnline($id, $artist_name, $song_title, $pdo) {
    // Verifica o status do cliente
    $status = checkAndUpdateStatus($id, $pdo);

    if ($status === 'online') {
        // Obter sessionKey e outras informações do cliente
        $sql = "SELECT * FROM acessos WHERE uniqueidclient = :id";
        $comando = $pdo->prepare($sql);
        $comando->bindParam(':id', $id, PDO::PARAM_STR);
        $comando->execute();
        $linha = $comando->fetch(PDO::FETCH_ASSOC);

        $sessionkey = $linha['apikeyscrobble'];
        $apiKey = $linha['char1'];
        $secret = $linha['char2'];
        $track = $song_title;

        if (empty($apiKey) || empty($secret)) {
            die("Chave de API ou segredo não configurados corretamente.");
        }

        $timestamp = time(); // Tempo UNIX da execução

        // Calcula a assinatura para o scrobble
        $apiSig = md5("api_key" . $apiKey . "artist" . $artist_name . "methodtrack.scrobble" . "sk" . $sessionkey . "timestamp" . $timestamp . "track" . $track . $secret);

        // Dados da requisição
        $data = [
            "method" => "track.scrobble",
            "api_key" => $apiKey,
            "sk" => $sessionkey,
            "artist" => $artist_name,
            "track" => $song_title,
            "timestamp" => $timestamp,
            "api_sig" => $apiSig,
            "format" => "json"
        ];

        // Configurações da requisição
        $options = [
            "http" => [
                "header" => "Content-Type: application/x-www-form-urlencoded",
                "method" => "POST",
                "content" => http_build_query($data)
            ]
        ];

        // Usando cURL para a requisição
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://ws.audioscrobbler.com/2.0/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            die("Erro na requisição para scrobble.");
        }

        echo "Scrobble enviado com sucesso para $id!<br>";
    } else {
        echo "$id não está online, não foi possível enviar o scrobble.<br>";
    }
}

// Chama a função de scrobble para as duas contas (gudu e soja)
scrobbleIfOnline('gudu', $artist_name, $song_title, $pdo);
scrobbleIfOnline('soja', $artist_name, $song_title, $pdo);

?>
