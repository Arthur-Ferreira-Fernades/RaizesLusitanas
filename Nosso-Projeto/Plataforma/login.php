<?php session_start();
$mensagem = $_SESSION['mensagem_login'] ?? null;
unset($_SESSION['mensagem_login']);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Login | Raízes Lusitanas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="styles/login.css" rel="stylesheet" />
</head>

<body class="login-bg">

    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="card shadow p-5 rounded-4 login-card">
            <?php if ($mensagem): ?>
                <div class="mensagem <?= $mensagem['tipo'] === 'sucesso' ? 'msg-sucesso' : 'msg-erro' ?>">
                    <?= htmlspecialchars($mensagem['texto']) ?>
                </div>
            <?php endif; ?>
            <br>
            <div class="logo-container mb-4">
                <img src="img/logo.png" alt="Raízes Lusitanas" class="logo-img" />
            </div>

            <h4 class="mb-4 text-center fw-bold" style="color: #c62828;">Acesso à Plataforma</h4>

            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['erro'];
                                                unset($_SESSION['erro']); ?></div>
            <?php endif; ?>

            <form action="scripts/processa_login.php" method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required autofocus placeholder="seu@email.com" />
                </div>
                <div class="mb-4">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" required placeholder="Digite sua senha" />
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold mb-3">Entrar</button>
                <br>
                <div class="form-recuperacao">
                    <a href="recupera_senha.php">Esqueceu sua senha?</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>