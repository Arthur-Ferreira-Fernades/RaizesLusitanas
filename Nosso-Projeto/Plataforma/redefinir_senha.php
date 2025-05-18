<?php
session_start();
require_once("scripts/conectaBanco.php");

$token = $_GET['token'] ?? '';

if (!$token) {
    echo "<p>Token inválido ou ausente.</p>";
    exit;
}

// Verifica o token no banco
$stmt = $conexao->prepare("SELECT UsuId, token_expira FROM usuarios WHERE token_recuperacao = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<p>Token inválido.</p>";
    exit;
}

$usuario = $resultado->fetch_assoc();

if (strtotime($usuario['token_expira']) < time()) {
    echo "<p>Token expirado.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles/login.css">
</head>

<body class="login-bg">
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="card shadow p-5 rounded-4 login-card">
            <div class="logo-container mb-4">
                <img src="img/logo.png" alt="Raízes Lusitanas" class="logo-img" />
            </div>
            <form action="scripts/processa_redefinicao.php" method="POST" class="formulario">
                <h2>Redefinir Senha</h2>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="mb-3">
                    <input type="password" name="nova_senha" placeholder="Nova senha" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="confirmar_senha" placeholder="Confirmar nova senha" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold mb-3">Atualizar Senha</button>
            </form>
        </div>
    </div>
</body>

</html>