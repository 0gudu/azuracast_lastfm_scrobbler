<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    die("Erro na conexão: " . $e->getMessage());
}

$sql = "SELECT * FROM acessos WHERE uniqueidclient = :id";
$comando = $pdo->prepare($sql);
$id = "gudu";  // Exemplo de ID que você quer buscar
$comando->bindParam(':id', $id, PDO::PARAM_STR); 

// Executa a consulta
$comando->execute();

// Captura o resultado
$linha = $comando->fetch(PDO::FETCH_ASSOC);

if (!$linha) {
    die("Erro: Não foi possível encontrar o sessionKey.");
}

// Obtendo o sessionKey
$sessionKey = $linha['apikeyscrobble']; 
$apiKey = $linha['char1']; 
$secret = $linha['char2']; 

// Defina suas variáveis corretamente

$url = 'https://ws.audioscrobbler.com/2.0/';  // URL da API do Last.fm

// Dados para o scrobble
$artist = "Os Mutantes";
$track = "Ela é minha menina";
$timestamp = time(); // Tempo UNIX da execução

// Calcula a assinatura para o scrobble
$apiSig = md5("api_key$apiKey" . "artist$artist" . "methodtrack.scrobble" . "sk$sessionKey" . "timestamp$timestamp" . "track$track" . $secret);

$data = [
    "method" => "track.scrobble",
    "api_key" => $apiKey,
    "sk" => $sessionKey,
    "artist" => $artist,
    "track" => $track,
    "timestamp" => $timestamp,
    "api_sig" => $apiSig,
    "format" => "json"
];

$options = [
    "http" => [
        "header" => "Content-Type: application/x-www-form-urlencoded",
        "method" => "POST",
        "content" => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

// Verifica se houve erro na requisição do scrobble
if ($response === FALSE) {
    die("Erro na requisição para scrobble.");
}

$jsonResponse = json_decode($response, true);

// Verifique se houve algum erro na resposta da API
if (isset($jsonResponse['error'])) {
    die("Erro na API: " . $jsonResponse['error']);
}

echo $response;
?>
