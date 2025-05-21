<?php 
session_start(); 
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['admin'] != true) {
  header("Location: login.php");
  exit;
}
?>
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
<header class="topo">
        <div class="container-header">
            <img src="img/logo.png" alt="Raízes Lusitanas" class="logo" />
            <nav>
                <span class="boas-vindas">Painel Administrativo</span>
                <a href="scripts/logOff.php" class="btn-sair">Sair</a>
            </nav>
        </div>
    </header>

<div class="container d-flex align-items-center justify-content-center">
  <div class="card login-card shadow rounded-4 w-100" style="max-width: 500px;">
    
    <!-- Logo compacta -->
    <div class="logo-container text-center mb-3">
      <img src="img/logo.png" alt="Raízes Lusitanas" class="logo-img" style="width: 90px;" />
    </div>

    <h5 class="mb-3 text-center fw-bold" style="color: #556b2f;">Crie uma Conta de Cliente</h5>

    <form action="scripts/processa_cadastro.php" method="POST">
      <!-- Informações básicas -->
      <div class="mb-2">
        <label class="form-label small-label">Nome completo</label>
        <input type="text" name="nome" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label class="form-label small-label">Data de nascimento</label>
        <input type="date" name="nascimento" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label class="form-label small-label">Nacionalidade</label>
        <input type="text" name="nacionalidade" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label class="form-label small-label">Estado Civil</label>
        <select name="estado_civil" class="form-control small-input" required>
          <option value="">Selecione</option>
          <option value="Solteiro(a)">Solteiro(a)</option>
          <option value="Casado(a)">Casado(a)</option>
          <option value="Divorciado(a)">Divorciado(a)</option>
          <option value="Viúvo(a)">Viúvo(a)</option>
        </select>
      </div>
      
      <!-- Contato -->
      <div class="mb-2">
        <label class="form-label small-label">Telefone</label>
        <input type="text" name="telefone" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label class="form-label small-label">E-mail</label>
        <input type="email" name="email" class="form-control small-input" required />
      </div>

      <!-- Endereço -->
      <div class="mb-2">
        <label class="form-label small-label">Endereço completo</label>
        <input type="text" name="endereco" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label class="form-label small-label">Cidade</label>
        <input type="text" name="cidade" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label class="form-label small-label">Estado</label>
        <input type="text" name="estado" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label class="form-label small-label">País</label>
        <input type="text" name="pais" class="form-control small-input" required />
      </div>

      <!-- Documentos -->
      <div class="mb-2">
        <label class="form-label small-label">CPF</label>
        <input type="text" name="cpf" class="form-control small-input" required />
      </div>
      <div class="mb-2">
        <label class="form-label small-label">RG</label>
        <input type="text" name="rg" class="form-control small-input" required />
      </div>

      <!-- Dados de acesso -->
      <div class="mb-2">
        <label class="form-label small-label">Senha</label>
        <input type="password" name="senha" class="form-control small-input" required />
      </div>

      <!-- Tipo de conta -->
      <div class="mb-3">
        <label class="form-label small-label">Tipo de Conta</label>
        <select name="Tipo" class="form-control small-input" required>
          <option value="">Selecione</option>
          <option value="cliente">Usuário</option>
          <option value="admin">Administrador</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary w-100 fw-bold mb-2">Cadastrar</button>
    </form>

    <a href="admin.php" class="btn btn-outline-primary w-100 fw-bold">Área Admin</a>

  </div>
</div>

</body>
</html>
