<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once("conectaBanco.php");

// Função de segurança para limpar entrada
function limpar($valor) {
  return htmlspecialchars(trim($valor));
}

// Coleta e validação
$nome     = limpar($_POST['nome'] ?? '');
$email    = limpar($_POST['email'] ?? '');
$telefone = limpar($_POST['telefone'] ?? '');
$senha    = $_POST['senha'] ?? '';

if (empty($nome) || empty($email) || empty($telefone) || empty($senha)) {
  $_SESSION['cadastro_erro'] = "Preencha todos os campos.";
  header("Location: ../cadastro.php");
  exit;
}

// Verifica se e-mail já existe
$sql = "SELECT UsuId FROM usuarios WHERE Email = ?";
$stmt = $conexao ->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
  $_SESSION['mensagem_registro'] = ['tipo' => 'erro', 'texto' => 'Email ja cadastrado.'];
  header("Location: ../cadastro.php");
  exit;
}
$stmt->close();

// Hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Inserção no banco
$sql = "INSERT INTO usuarios (Nome, Email, Telefone, Senha) VALUES (?, ?, ?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ssss", $nome, $email, $telefone, $senha_hash);

if ($stmt->execute()) {
  $_SESSION['mensagem_registro'] = ['tipo' => 'sucesso', 'texto' => 'Cadastro realizado com sucesso! Faça login.'];
  header("Location: ../cadastro.php");
  exit;
} else {
  $_SESSION['mensagem_registro'] = ['tipo' => 'erro', 'texto' => 'Erro realizar cadastro. Tente novamente.'];
  header("Location: ../cadastro.php");
  exit;
}
