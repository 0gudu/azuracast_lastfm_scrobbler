<?php

if (empty($_GET['apikey']) || empty($_GET['secret']) || empty($_GET['token'])) {
    die("Erro: Parâmetros obrigatórios não fornecidos.");
}

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

$token = $_GET['token'];       
$apiKey = $_GET['apikey'];
$secret = $_GET['secret'];

$stmt = $pdo->prepare("UPDATE acessos SET char1 = :apikey, char2 = :secrett, char4 = :token WHERE uniqueidclient = 'gudu'");	
$stmt->bindParam(':apikey', $apiKey);
$stmt->bindParam(':secrett', $secret);
$stmt->bindParam(':token', $token);

try {
    $stmt->execute();
} catch (PDOException $e) {
    echo "Erro ao inserir dados na coluna char4: " . $e->getMessage();
    exit;
}



// Calcula a assinatura para obter a sessão
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

// Verifica se houve erro na requisição
if ($response === FALSE) {
    die("Erro na requisição para obter a sessão.");
}

$json = json_decode($response, true);

// Verifica se a chave de sessão foi obtida corretamente
if (isset($json["session"]["key"])) {
    $sessionKey = $json["session"]["key"]; // Salve essa chave para scrobblar!
} else {
    die("Erro ao obter a chave de sessão.");
}

$stmt = $pdo->prepare("UPDATE acessos SET apikeyscrobble = :sessionss WHERE uniqueidclient = 'gudu'");	
$stmt->bindParam(':sessionss', $sessionKey);

try {
    $stmt->execute();
    echo "Chave de sessão atualizada com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao inserir dados na coluna apikeyscrobble: " . $e->getMessage();
}
?>
