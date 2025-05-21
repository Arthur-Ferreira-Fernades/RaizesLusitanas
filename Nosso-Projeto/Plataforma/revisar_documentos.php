<?php
include "scripts/conectaBanco.php";

$usuId = (int)$_GET['usuId'];
$processoId = (int)$_GET['processoId'];

$tipoQuery = "SELECT Tipo FROM processos WHERE ProcId = $processoId";
$tipoRes = mysqli_query($conexao, $tipoQuery);

if (!$tipoRes) {
    die("Erro na consulta SQL (tipo de processo): " . mysqli_error($conexao));
}

$tipoRow = mysqli_fetch_assoc($tipoRes);
$tipo = $tipoRow['Tipo'] ?? '';


// Array com documentos esperados (usando o que criamos antes)
$documentosPorTipo = [
    'Visto de Residência' => [
        ['nome' => 'Passaporte válido', 'icone' => 'fa-solid fa-passport'],
        ['nome' => 'Formulário de pedido de visto preenchido', 'icone' => 'fa-solid fa-file-alt'],
        ['nome' => 'Duas fotos 3x4 recentes', 'icone' => 'fa-solid fa-camera'],
        ['nome' => 'Atestado de antecedentes criminais do país de origem', 'icone' => 'fa-solid fa-shield-alt'],
        ['nome' => 'Comprovante de meios de subsistência', 'icone' => 'fa-solid fa-wallet'],
        ['nome' => 'Comprovante de alojamento', 'icone' => 'fa-solid fa-home'],
        ['nome' => 'Seguro de saúde', 'icone' => 'fa-solid fa-heartbeat'],
        ['nome' => 'Justificativa/documentação que comprove o motivo do visto', 'icone' => 'fa-solid fa-file-lines'],
        ['nome' => 'NIF', 'icone' => 'fa-solid fa-id-card'],
    ],
    'Nacionalidade Portuguesa Por Descendencia' => [
        ['nome' => 'Certidão de nascimento do requerente (apostilada e traduzida)', 'icone' => 'fa-solid fa-file-alt'],
        ['nome' => 'Certidão de nascimento do pai/mãe português transcrita em Portugal', 'icone' => 'fa-solid fa-user'],
        ['nome' => 'Documentos que comprovem ligação efetiva à comunidade portuguesa', 'icone' => 'fa-solid fa-users'],
        ['nome' => 'Passaporte ou Documento de Identidade válido', 'icone' => 'fa-solid fa-passport'],
        ['nome' => 'Atestado de antecedentes criminais', 'icone' => 'fa-solid fa-shield-alt'],
        ['nome' => 'Formulário oficial preenchido', 'icone' => 'fa-solid fa-file-alt'],
    ],
    'Nacionalidade Por Casamento' => [
        ['nome' => 'Passaporte válido', 'icone' => 'fa-solid fa-passport'],
        ['nome' => 'Certidão de nascimento do requerente (apostilada e traduzida)', 'icone' => 'fa-solid fa-file-alt'],
        ['nome' => 'Certidão de casamento transcrita em Portugal', 'icone' => 'fa-solid fa-ring'],
        ['nome' => 'Certidão de nascimento do cônjuge português', 'icone' => 'fa-solid fa-user'],
        ['nome' => 'Comprovante de ligação efetiva à comunidade portuguesa', 'icone' => 'fa-solid fa-users'],
        ['nome' => 'Atestado de antecedentes criminais do país de origem', 'icone' => 'fa-solid fa-shield-alt'],
        ['nome' => 'Atestado de antecedentes criminais de Portugal', 'icone' => 'fa-solid fa-shield-alt'],
    ],
    'Reagrupamento Familiar' => [
        ['nome' => 'Passaporte válido do Requerente', 'icone' => 'fa-solid fa-passport'],
        ['nome' => 'Passaporte válido do Familiar Residente', 'icone' => 'fa-solid fa-passport'],
        ['nome' => 'Comprovante de residência do familiar', 'icone' => 'fa-solid fa-home'],
        ['nome' => 'Comprovativo de vínculo familiar', 'icone' => 'fa-solid fa-users'],
        ['nome' => 'Comprovante de rendimentos', 'icone' => 'fa-solid fa-wallet'],
        ['nome' => 'Declaração de responsabilidade do familiar', 'icone' => 'fa-solid fa-file-signature'],
        ['nome' => 'Atestado de antecedentes criminais', 'icone' => 'fa-solid fa-shield-alt'],
        ['nome' => 'Comprovativo de alojamento (ex: contrato de arrendamento)', 'icone' => 'fa-solid fa-home'],
        ['nome' => 'NIF (Número de Identificação Fiscal)', 'icone' => 'fa-solid fa-id-card'],
    ],
    'Nacionalidade Por Tempo de Residencia' => [
        ['nome' => 'Passaporte válido', 'icone' => 'fa-solid fa-passport'],
        ['nome' => 'Título de residência válido', 'icone' => 'fa-solid fa-id-badge'],
        ['nome' => 'Certidão de nascimento (apostilada e traduzida)', 'icone' => 'fa-solid fa-file-alt'],
        ['nome' => 'Atestado de antecedentes criminais do país de origem', 'icone' => 'fa-solid fa-shield-alt'],
        ['nome' => 'Atestado de antecedentes criminais de Portugal', 'icone' => 'fa-solid fa-shield-alt'],
        ['nome' => 'Comprovante de residência legal por no mínimo 5 anos', 'icone' => 'fa-solid fa-calendar-check'],
        ['nome' => 'Declaração de ligação à comunidade portuguesa', 'icone' => 'fa-solid fa-users'],
        ['nome' => 'Requerimento assinado (modelo oficial)', 'icone' => 'fa-solid fa-pen'],
        ['nome' => 'NIF e comprovante de situação fiscal', 'icone' => 'fa-solid fa-id-card'],
    ],
    'Cidadania Portuguesa' => [
        ['nome' => 'Certidão de nascimento (apostilada e traduzida)', 'icone' => 'fa-solid fa-file-alt'],
        ['nome' => 'Documentos de ascendência (pais ou avós portugueses)', 'icone' => 'fa-solid fa-user-friends'],
        ['nome' => 'Passaporte válido', 'icone' => 'fa-solid fa-passport'],
        ['nome' => 'Prova de vínculo com a comunidade portuguesa', 'icone' => 'fa-solid fa-users'],
        ['nome' => 'Atestado de antecedentes criminais', 'icone' => 'fa-solid fa-shield-alt'],
        ['nome' => 'Comprovantes de residência', 'icone' => 'fa-solid fa-home'],
        ['nome' => 'NIF', 'icone' => 'fa-solid fa-id-card'],
    ]
];

$documentosEsperados = $documentosPorTipo[$tipo] ?? [];

$query = "SELECT * FROM documentos WHERE ProcId = $processoId";
$result = mysqli_query($conexao, $query);

$documentosEnviados = [];
while ($doc = mysqli_fetch_assoc($result)) {
    $documentosEnviados[$doc['NomeDocumento']] = $doc;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/revisar_documentos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <title>Revisar Documentos</title>
</head>
<header class="topo">
    <div class="container-header">
        <img src="img/logo.png" alt="Raízes Lusitanas" class="logo" />
        <nav>
            <span class="boas-vindas">Painel Administrativo</span>
            <a href="scripts/logOff.php" class="btn-sair">Sair</a>
        </nav>
    </div>
</header>

<body class="bg-light">
    <div class="conteudo container d-flex justify-content-center flex-column py-4">
        <h2 class="mb-4">Revisar Documentos do Cliente</h2><br>
        <div class="container d-flex flex-wrap justify-content-center">
            <?php foreach ($documentosEsperados as $doc) {
                $nome = $doc['nome'];
                $icone = $doc['icone'];
                $docInfo = $documentosEnviados[$nome] ?? null;

                // Define o status e a classe de cor
                $status = isset($docInfo['Status']) ? trim(strtolower($docInfo['Status'])) : 'não enviado';
                $classeStatus = match ($status) {
                    'aprovado' => 'success',
                    'reprovado' => 'danger',
                    'em análise' => 'warning',
                    default => 'secondary'
                };

                // Ícone Font Awesome formatado corretamente (remove o prefixo caso tenha sido duplicado)
                $icone = str_replace('fa-solid fa-', '', $icone);

                // Botões ou informações adicionais
                $botao = '';
                if ($docInfo) {
                    if ($status === 'em análise') {
                        $botao = '
                <form method="post" action="scripts/processar_revisao.php" class="mt-2">
                    <input type="hidden" name="docId" value="' . $docInfo['DocumentoId'] . '">
                    <textarea name="observacao" class="form-control mb-2" placeholder="Observação (se reprovar)"></textarea>
                    <div class="d-flex justify-content-center gap-2">
                        <button name="acao" value="aprovar" class="btn btn-success btn-sm">Aprovar</button>
                        <button name="acao" value="reprovar" class="btn btn-danger btn-sm">Reprovar</button>
                    </div>
                </form>';
                    } elseif ($status === 'reprovado') {
                        $botao = '<p class="mt-2 text-danger"><strong>Observação:</strong><br>' . nl2br(htmlspecialchars($docInfo['Observacao'])) . '</p>';
                    }

                    $botao = '<a href="' . $docInfo['CaminhoArquivo'] . '" target="_blank" class="btn btn-primary btn-sm mb-2">Visualizar Documento</a><br>' . $botao;
                } else {
                    $botao = '<p class="text-danger mt-2">Documento ainda não enviado.</p>';
                }

                echo '
    <div class="card m-2" style="width: 18rem;">
        <div class="card-body text-center">
            <i class="fas fa-' . htmlspecialchars($icone) . ' fa-2x mb-2"></i>
            <h5 class="card-title">' . htmlspecialchars($nome) . '</h5>
            <span class="badge bg-' . $classeStatus . '">' . htmlspecialchars($status) . '</span>
            ' . $botao . '
        </div>
    </div>';
            } ?>
        </div>

    </div>
</body>

</html>