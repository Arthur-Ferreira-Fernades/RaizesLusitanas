<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Senha - Raízes Lusitanas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/login.css">
  <link rel="stylesheet" href="styles/recupera_senha.css">
</head>
<body class="login-bg">
  <div class="container-recupera login-card">
    <h2>Recuperar Senha</h2>
    <form method="POST" action="scripts/processa_recuperacao.php">
      <label for="email" class="form-label">Digite seu e-mail cadastrado:</label>
      <input type="email" name="email" id="email" class="form-control" required>

      <button type="submit" class="btn btn-primary w-100 fw-bold mb-3">Enviar Instruções</button>
    </form>

    <div class="voltar-login">
      <a href="login.php">Voltar para o login</a>
    </div>
  </div>
</body>
</html>
