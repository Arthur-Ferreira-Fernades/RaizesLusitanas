<?php
require_once("scripts/conectaBanco.php");
session_start();

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['admin'] != true) {
    header("Location: login.php");
    exit;
}

// Buscar todos os clientes
$clientes = $conexao->query("SELECT UsuId, Nome FROM usuarios WHERE Tipo = 'cliente' ORDER BY Nome ASC");

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuarioId = $_POST['usuario'];
    $tipo = $_POST['tipo_processo'];
    $dataAtual = date('Y-m-d');

    $stmt = $conexao->prepare("INSERT INTO processos (UsuId, Tipo, Status, DataPedido, UltimaAtualizacao, Porcentagem) VALUES (?, ?, 'Iniciado', ?, ?, 0)");
    $stmt->bind_param("isss", $usuarioId, $tipo, $dataAtual, $dataAtual);

    if ($stmt->execute()) {
        $mensagem = "✅ Processo criado com sucesso!";
    } else {
        $mensagem = "❌ Erro ao criar processo: " . $conexao->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Novo Processo</title>
    <link rel="stylesheet" href="styles/admin.css">
    <link rel="stylesheet" href="styles/criar_processo.css">
</head>
<body>
<header class="topo">
        <div class="container-header">
            <img src="img/logo.png" alt="Raízes Lusitanas" class="logo" />
            <nav>
                <span class="boas-vindas">Painel Administrativo</span>
                <a href="scripts/logOff.php" class="btn-sair">Sair</a>
            </nav>
        </div>
    </header>
    <div class="form-container">
        <h2>Criar Novo Processo</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="usuario">Cliente:</label>
            <select name="usuario" required>
                <option value="">Selecione um cliente</option>
                <?php while ($cliente = $clientes->fetch_assoc()): ?>
                    <option value="<?= $cliente['UsuId'] ?>"><?= htmlspecialchars($cliente['Nome']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="tipo_processo">Tipo de Processo:</label>
            <select name="tipo_processo" required>
                <option value="">Selecione um tipo</option>
                <option value="Cidadania Portuguesa">Cidadania Portuguesa</option>
                <option value="Nacionalidade Por Descendencia">Nacionalidade Por Descendência</option>
                <option value="Nacionalidade Por Casamento">Nacionalidade Por Casamento</option>
                <option value="Nacionalidade Por Tempo de Residencia">Nacionalidade Por Tempo de Residência</option>
                <option value="Visto de Residência">Visto de Residência</option>
                <option value="Reagrupamento Familiar">Reagrupamento Familiar</option>
            </select>

            <button type="submit">Criar Processo</button>
        </form>

        <a href="admin.php" class="voltar-link">← Voltar ao painel</a>
    </div>
</body>
</html>
