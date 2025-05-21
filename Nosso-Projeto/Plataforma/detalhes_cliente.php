<?php
require_once("scripts/conectaBanco.php");
session_start();

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['admin'] != true) {
    header("Location: login.php");
    exit;
}

$procId = $_GET['proc_id'] ?? null;

if (!$procId) {
    echo "Código do processo inválido.";
    exit;
}

$sql = "SELECT u.Nome, u.Email, u.Telefone,u.nascimento,u.estado_civil,u.endereco,u.cidade,u.estado,u.pais,u.cpf,u.rg, p.*
        FROM usuarios u
        INNER JOIN processos p ON u.UsuId = p.UsuId
        WHERE p.ProcId = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $procId);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();

if (!$dados) {
    echo "Processo não encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Cliente</title>
    <link rel="stylesheet" href="styles/admin.css">
    <link rel="stylesheet" href="styles/detalhes_cliente.css">
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
    <div class="detalhes-container">
        <h2>Informações do Cliente e Processo</h2>

        <div class="campo"><strong>Código do Processo:</strong> <?= htmlspecialchars($dados['ProcId']) ?></div>
        <div class="campo"><strong>Nome:</strong> <?= htmlspecialchars($dados['Nome']) ?></div>
        <div class="campo"><strong>Email:</strong> <?= htmlspecialchars($dados['Email']) ?></div>
        <div class="campo"><strong>Telefone:</strong> <?= htmlspecialchars($dados['Telefone'] ?? '-') ?></div>
        <div class="campo"><strong>Nascimento:</strong> <?= htmlspecialchars($dados['nascimento'] ?? '-') ?></div>
        <div class="campo"><strong>Estado Civil:</strong> <?= htmlspecialchars($dados['estado_civil'] ?? '-') ?></div>
        <div class="campo"><strong>Endereço:</strong> <?= htmlspecialchars($dados['endereco'] ?? '-') ?></div>
        <div class="campo"><strong>Cidade:</strong> <?= htmlspecialchars($dados['cidade'] ?? '-') ?></div>
        <div class="campo"><strong>Estado:</strong> <?= htmlspecialchars($dados['estado'] ?? '-') ?></div>
        <div class="campo"><strong>País:</strong> <?= htmlspecialchars($dados['pais'] ?? '-') ?></div>
        <div class="campo"><strong>RG:</strong> <?= htmlspecialchars($dados['rg'] ?? '-') ?></div>
        <div class="campo"><strong>CPF:</strong> <?= htmlspecialchars($dados['cpf'] ?? '-') ?></div>
        <div class="campo"><strong>Tipo de Processo:</strong> <?= htmlspecialchars($dados['Tipo']) ?></div>
        <div class="campo"><strong>Status Atual:</strong> <?= nl2br(htmlspecialchars($dados['Status'])) ?></div>
        <div class="campo"><strong>Progresso:</strong> <?= intval($dados['Porcentagem']) ?>%</div>
        <div class="campo"><strong>Data do Pedido:</strong> <?= date('d/m/Y', strtotime($dados['DataPedido'])) ?></div>
        <div class="campo"><strong>Última Atualização:</strong> <?= date('d/m/Y', strtotime($dados['UltimaAtualizacao'])) ?></div>

        <a href="admin.php" class="voltar">← Voltar ao Painel</a>
    </div>
</body>
</html>
