<?php
require_once("scripts/conectaBanco.php");
session_start();

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['admin'] != true) {
    header("Location: login.php");
    exit;
}

$procId = $_GET['proc_id'] ?? null;
if (!$procId) {
    header("Location: admin.php");
    exit;
}

$stmt = $conexao->prepare("SELECT Porcentagem, Status, Tipo, EtapaAtual FROM processos WHERE ProcId = ?");
$stmt->bind_param("i", $procId);
$stmt->execute();
$result = $stmt->get_result();
$processo = $result->fetch_assoc();

if (!$processo) {
    echo "Processo não encontrado.";
    exit;
}

$tipoProcesso = $processo['Tipo'];
$etapas = [];

switch ($tipoProcesso) {
    case 'Visto de Residência':
        $etapas = [
            1 => "Escolha do tipo de visto adequado",
            2 => "Checklist e coleta de documentos",
            3 => "Agendamento com o consulado",
            4 => "Preenchimento do formulário de pedido",
            5 => "Comparecimento ao consulado para entrega",
            6 => "Aguardar análise e resposta do consulado",
            7 => "Recebimento do visto",
            8 => "Agendamento de chegada em Portugal (SEF)",
            9 => "Legalização em Portugal (Título de residência)",
            10 => "Concluído"
        ];
        break;
    case 'Cidadania Portuguesa':
        $etapas = [
            1 => "Documentação analisada",
            2 => "Registro no consulado",
            3 => "Protocolo em Portugal",
            4 => "Processo em tramitação",
            5 => "Cidadania concedida",
            6 => "Concluído"
        ];
        break;
    case 'Nacionalidade Por Descendencia':
        $etapas = [
            1 => "Análise inicial do caso",
            2 => "Coleta dos documentos",
            3 => "Tradução e apostilamento de documentos",
            4 => "Preenchimento do formulário de requerimento",
            5 => "Envio do processo para Conservatória",
            6 => "Recebimento e conferência do número do processo",
            7 => "Acompanhamento do processo",
            8 => "Decisão final / deferimento",
            9 => "Envio da certidão portuguesa ao cliente",
            10 => "Concluído"

        ];
        break;
    case 'Nacionalidade Por Casamento':
        $etapas = [
            1 => "Análise do tempo de casamento e elegibilidade",
            2 => "Coleta e apostilamento de documentos",
            3 => "Declaração de vínculo afetivo",
            4 => "Tradução juramentada",
            5 => "Preenchimento do formulário de pedido",
            6 => "Envio para Conservatória",
            7 => "Recebimento do número do processo",
            8 => "Acompanhamento da tramitação",
            9 => "Decisão final",
            10 => "Entrega da certidão ao cliente",
            11 => "Concluído"
        ];
        break;
    case 'Nacionalidade Por Tempo de Residencia':
        $etapas = [
            1 => "Verificação do tempo de residência legal",
            2 => "Reunião de documentos",
            3 => "Certidões apostiladas e traduzidas",
            4 => "Preenchimento do pedido",
            5 => "Entrega do pedido em Portugal ",
            6 => "Recebimento do número do processo",
            7 => "Acompanhamento",
            8 => "Decisão e entrega do documento final",
            9 => "Concluído"
        ];
        break;
    case 'Reagrupamento Familiar':
        $etapas = [
            1 => "Verificação da elegibilidade (residente principal)",
            2 => "Coleta de documentos do familiar",
            3 => "Preparação de documentos do residente principal",
            4 => "Traduções e apostilamentos",
            5 => "Envio do pedido ao SEF (Portugal)",
            6 => "Acompanhamento do processo",
            6 => "Aprovação e autorização de residência",
            6 => "Agendamento da chegada do familiar",
            6 => "Concluído"
        ];
        break;
    default:
        $etapas = [1 => "Etapa desconhecida"];
}

$totalEtapas = count($etapas);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Atualizar Processo</title>
    <link rel="stylesheet" href="styles/admin.css">
    <link rel="stylesheet" href="styles/atualiza_status.css">
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
        <h2>Atualizar Processo: #<?= htmlspecialchars($procId) ?></h2>
        <form method="POST" action="scripts/salva_status.php">
            <input type="hidden" name="proc_id" value="<?= htmlspecialchars($procId) ?>">
            <input type="hidden" name="tipo_processo" value="<?= htmlspecialchars($tipoProcesso) ?>">
            <input type="hidden" name="total_etapas" value="<?= $totalEtapas ?>">

            <h3>Tipo de Processo: <?= htmlspecialchars($tipoProcesso) ?></h3>

            <label for="etapa">Selecione a Etapa Atual:</label>
            <select name="etapa" required>
                <?php foreach ($etapas as $indice => $descricao): ?>
                    <option value="<?= $indice ?>" <?= ($indice == $processo['EtapaAtual']) ? 'selected' : '' ?>>
                        <?= $indice ?> - <?= $descricao ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <h2>Porcentagem atual:</h2>
            <div class="barra-progresso">
                <div class="barra-preenchida" style="width: <?= intval($processo['Porcentagem']) ?>%;"></div>
            </div>
            <span class="texto-porcentagem"><?= intval($processo['Porcentagem']) ?>%</span>


            <button type="submit">Salvar Alterações</button>
        </form>

        <a href="admin.php" class="voltar">← Voltar ao Painel</a>
    </div>

</body>

</html>