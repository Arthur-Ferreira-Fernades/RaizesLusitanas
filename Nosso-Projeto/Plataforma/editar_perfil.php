<?php
session_start();
require_once("scripts/conectaBanco.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$mensagem = "";

// Atualizar dados se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $senha = $_POST['senha'];

    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET Nome = ?, Email = ?, Telefone = ?, Senha = ? WHERE UsuId = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $email, $telefone, $senhaHash, $usuarioId);
    } else {
        $sql = "UPDATE usuarios SET Nome = ?, Email = ?, Telefone = ? WHERE UsuId = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sssi", $nome, $email, $telefone, $usuarioId);
    }

    if ($stmt->execute()) {
        $_SESSION['usuario_nome'] = $nome;
        $mensagem = "Dados atualizados com sucesso.";
    } else {
        $mensagem = "Erro ao atualizar dados.";
    }
}

// Buscar dados atuais
$sql = "SELECT Nome, Email, Telefone FROM usuarios WHERE UsuId = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <link rel="stylesheet" href="styles/editar_perfil.css">
  <style>
    
  </style>
</head>
<body>
<header class="topo">
  <div class="container-header">
    <img src="img/logo.png" alt="Raízes Lusitanas" class="logo" />
    <nav>
      <a href="dashboard.php" class="btn-sair">Dashboard</a>
      <a href="scripts/logOff.php" class="btn-sair">Sair</a>
    </nav>
  </div>
</header>

<div class="container-edicao">
  <h2>Editar Perfil</h2>
  <?php if ($mensagem): ?>
    <p style="color: green;"><?= $mensagem ?></p>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label>Nome:</label><br>
      <div class="grupo">
      <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['Nome']) ?>" readonly width="500px" required>
      <button type="button" onclick="habilitarCampo('nome')">Editar</button>
      </div>
    </div>

    <div class="form-group">
      <label>Email:</label><br>
      <div class="grupo">
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['Email']) ?>" readonly required>
      <button type="button" onclick="habilitarCampo('email')">Editar</button>
      </div>
    </div>

    <div class="form-group">
      <label>Telefone:</label><br>
      <div class="grupo">
      <input type="text" name="telefone" id="telefone" value="<?= htmlspecialchars($usuario['Telefone']) ?>" readonly required>
      <button type="button" onclick="habilitarCampo('telefone')">Editar</button>
      </div>
    </div>

    <div class="form-group">
      <label>Nova Senha (opcional):</label><br>
      <div class="grupo">
      <input type="password" name="senha" id="senha" readonly>
      <button type="button" onclick="habilitarCampo('senha')">Editar</button>
      </div>
    </div>

    <button type="submit">Salvar Alterações</button>
    <a href="dashboard.php">Cancelar</a>
  </form>
</div>

<script>
  function habilitarCampo(id) {
    const input = document.getElementById(id);
    input.removeAttribute('readonly');
    input.focus();
  }
</script>
</body>
</html>
