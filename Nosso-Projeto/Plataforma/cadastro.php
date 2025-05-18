<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Cadastro | Raízes Lusitanas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="styles/cadastro.css" rel="stylesheet" />
</head>
<body class="login-bg">

<div class="container d-flex align-items-center justify-content-center vh-100">
  <div class="card login-card shadow rounded-4 w-100" style="max-width: 400px;">
    
    <!-- Logo compacta -->
    <div class="logo-container text-center mb-3">
      <img src="img/logo.png" alt="Raízes Lusitanas" class="logo-img" style="width: 90px;" />
    </div>

    <h5 class="mb-3 text-center fw-bold" style="color: #556b2f;">Crie sua Conta</h5>

    <?php if (isset($_SESSION['cadastro_erro'])): ?>
      <div class="alert alert-danger p-2"><?php echo $_SESSION['cadastro_erro']; unset($_SESSION['cadastro_erro']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['cadastro_sucesso'])): ?>
      <div class="alert alert-success p-2"><?php echo $_SESSION['cadastro_sucesso']; unset($_SESSION['cadastro_sucesso']); ?></div>
    <?php endif; ?>

    <form action="scripts/processa_cadastro.php" method="POST">
      <div class="mb-2">
        <label for="nome" class="form-label small-label">Nome completo</label>
        <input type="text" name="nome" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label for="email" class="form-label small-label">E-mail</label>
        <input type="email" name="email" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label for="telefone" class="form-label small-label">Telefone</label>
        <input type="text" name="telefone" class="form-control small-input" required />
      </div>
      <div class="mb-3">
        <label for="senha" class="form-label small-label">Senha</label>
        <input type="password" name="senha" class="form-control small-input" required />
      </div>
      <button type="submit" class="btn btn-primary w-100 fw-bold mb-2">Cadastrar</button>
    </form>

    <a href="login.php" class="btn btn-outline-primary w-100 fw-bold">Já tenho conta</a>

  </div>
</div>

</body>
</html>
