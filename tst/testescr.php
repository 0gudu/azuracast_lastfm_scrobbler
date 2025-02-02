<?php
$dbname = 'accesscontrol';
$host = 'localhost';
$dbuser = 'root'; 
$dbpass = 'nova_senha';

try {
    $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

$id = 'gudu';
$sql = "SELECT * FROM acessos WHERE uniqueidclient = :id";
$comando = $pdo->prepare($sql);
$comando->bindParam(':id', $id, PDO::PARAM_STR);
$comando->execute();
$linha = $comando->fetch(PDO::FETCH_ASSOC);

if (!$linha) {
    die("Erro: Não foi possível encontrar o sessionKey.");
}

$sessionkey = $linha["apikeyscrobble"];
$apiKey = $linha["char1"]; 
$secret = $linha["char2"];

// Dados para o scrobble
$artist_name = "Os Mutantes";
$song_title = "Ela é minha menina";
$timestamp = time(); 

// Geração da assinatura
$apiSig = md5("api_key" . $apiKey . "artist" . $artist_name . "methodtrack.scrobble" . "sk" . $sessionkey . "timestamp" . $timestamp . "track" . $song_title . $secret);

// Dados para a requisição
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

// Inicializa cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://ws.audioscrobbler.com/2.0/');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Exibe a resposta
echo $response;
?>
