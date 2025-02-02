<?php


$sessionKey 
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

echo $response;