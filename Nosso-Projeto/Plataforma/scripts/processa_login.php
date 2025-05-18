<?php
session_start();
require_once("conectaBanco.php");

// Limpa entradas
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
  $_SESSION['login_erro'] = "Preencha todos os campos.";
  header("Location: ../login.php");
  exit;
}

// Verifica se o usuário existe
$sql = "SELECT UsuId, Nome, Senha, Tipo FROM usuarios WHERE Email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
  if (password_verify($senha, $user['Senha'])) {
    $_SESSION['usuario_id'] = $user['UsuId'];
    $_SESSION['usuario_nome'] = $user['Nome'];
    $_SESSION['usuario_tipo'] = $user['Tipo'];
    
    // Se for admin, define flag de admin e redireciona para painel admin
    if ($user['Tipo'] === 'admin') {
      $_SESSION['admin'] = true;
      header("Location: ../admin.php");
    } else {
      $_SESSION['admin'] = false;
      header("Location: ../dashboard.php");
    }
    exit;
  } else {
    $_SESSION['login_erro'] = "Senha incorreta.";
    $_SESSION['mensagem_login'] = ['tipo' => 'erro', 'texto' => 'Usuário ou senha incorretos.'];
    header("Location: ../login.php");
    exit;
  }
} else {
  $_SESSION['mensagem_login'] = ['tipo' => 'erro', 'texto' => 'Usuário não encontrado.'];
  header("Location: ../login.php?Erro=1");
  exit;
}
