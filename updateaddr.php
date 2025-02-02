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
    $pr = 0;
} catch (PDOException $e) {
    $pr = 1;
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

$addr = isset($_GET['addrs']) ? $_GET['addrs'] : '';  
$nome = isset($_GET['nome']) ? $_GET['nome'] : '';    

$stmt = $pdo->prepare("UPDATE acessos set char3 = :ipzao where uniqueidclient = :nome");
$stmt->bindParam(':ipzao', $addr);  
$stmt->bindParam(':nome', $nome);

try {
    $stmt->execute();
    echo "Dados inseridos com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao inserir dados: " . $e->getMessage();
}
