<?php
// Consulta SQL
$sql = "SELECT * FROM cc WHERE id = :id";
$comando = $pdo->prepare($sql);

// Substituindo o valor do parâmetro :id
$id = 1;  // Exemplo de ID que você quer buscar
$comando->bindParam(':id', $id, PDO::PARAM_INT);

// Executa a consulta
$comando->execute();

// Captura os resultados
$linhas = $comando->fetch(PDO::FETCH_ASSOC);

// Acessando o valor de uma coluna
if ($linhas) {
    $m = $linhas["dat_fechacc"];  // Valor da coluna 'dat_fechacc'
    $n = $linhas["id"];  // Valor da coluna 'id'

    echo "ID: " . $n . " - Data de Fechamento: " . $m;
} else {
    echo "Nenhum resultado encontrado.";
}




$stmt = $pdo->prepare("INSERT INTO movs (title, cat, value, date, type, cc) VALUES (:title, :cat, :value, :datas, :type, :cc)");	
$stmt->bindParam(':title', $title);
$stmt->bindParam(':cat', $cat);
$stmt->bindParam(':value', $value);
$stmt->bindParam(':datas', $date);
$stmt->bindParam(':type', $type);
$stmt->bindParam(':cc', $isCC);

$stmt->execute();



$apiKey = "SUA_API_KEY";
$secret = "SEU_SHARED_SECRET";
$token = "TOKEN_QUE_O_USUARIO_RECEBEU"; // Pegue da URL de callback

$apiSig = md5("api_key$apiKey" . "methodauth.getSession" . "token$token" . $secret);

$url = "https://ws.audioscrobbler.com/2.0/";
$data = [
    "method" => "auth.getSession",
    "api_key" => $apiKey,
    "token" => $token,
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
$json = json_decode($response, true);

$sessionKey = $json["session"]["key"]; // Salve essa chave para scrobblar!



$sessionKey = "SESSION_KEY_AQUI"; // Obtido no passo anterior
$artist = "Nome do Artista";
$track = "Nome da Música";
$timestamp = time(); // Tempo UNIX da execução

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
echo $response;
