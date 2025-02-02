<?php
$dbname = 'accesscontrol';
$host = 'localhost';
$dbuser = 'root'; 
$dbpass = 'nova_senha';

try {
    $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pr = 0;
} catch (PDOException $e) {
    $pr = 1;
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

$addr = $_GET['addr'];

$date = date('Y-m-d H:i:s'); 
$stmt = $pdo->prepare("UPDATE acessos set char5 = :dataa where char3 = :addr");	
$stmt->bindParam(':dataa', $date);
$stmt->bindParam(':addr', $addr);

$stmt->execute();

