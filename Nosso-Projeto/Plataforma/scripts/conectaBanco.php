<?php
$host = 'localhost';
$db = 'plataforma_imigracao';
$user = 'root';
$senha = ''; // Altere conforme seu ambiente

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexao = new mysqli($host, $user, $senha, $db);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
