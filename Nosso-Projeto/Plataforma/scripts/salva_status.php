<?php
require_once("conectaBanco.php");
session_start();

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['admin'] != true) {
    header("Location: ../login.php");
    exit;
}

$procId = $_POST['proc_id'] ?? null;
$etapa = intval($_POST['etapa'] ?? 0);
$totalEtapas = intval($_POST['total_etapas'] ?? 1);
$tipo = $_POST['tipo_processo'] ?? "";

if (!$procId || $etapa < 1 || $etapa > $totalEtapas) {
    echo "Dados inválidos.";
    exit;
}

// Define as etapas por tipo
$etapas = [];
switch ($tipo) {
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
    case 'Nacionalidade Por Casamento"':
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

$porcentagem = intval(($etapa / $totalEtapas) * 100);
$status = $etapas[$etapa] ?? "Etapa indefinida";
$dataHoraAtual = date('Y-m-d H:i:s');

$stmt = $conexao->prepare("UPDATE processos SET EtapaAtual = ?, Porcentagem = ?, Status = ?, UltimaAtualizacao = ? WHERE ProcId = ?");
$stmt->bind_param("iissi", $etapa, $porcentagem, $status, $dataHoraAtual, $procId);
$stmt->execute();

header("Location: ../admin.php");
exit;
