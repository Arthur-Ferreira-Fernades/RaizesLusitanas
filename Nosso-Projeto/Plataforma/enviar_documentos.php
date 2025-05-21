<?php

session_start();
require_once "scripts/conectaBanco.php";

if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

$usuarioId = $_SESSION['usuario_id'];
$nomeUsuario = $_SESSION['usuario_nome'];
$procId = $_GET['proc_id'] ?? null;

if (!$procId) {
  echo "ID do processo não fornecido.";
  exit();
}

// DOCUMENTOS POR TIPO DE PROCESSO
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



// BUSCA TIPO DO PROCESSO
$sqlTipo = "SELECT Tipo FROM processos WHERE ProcId = ? AND UsuId = ?";
$stmtTipo = $conexao->prepare($sqlTipo);
$stmtTipo->bind_param("ii", $procId, $usuarioId);
$stmtTipo->execute();
$stmtTipo->bind_result($tipoProcesso);
$stmtTipo->fetch();
$stmtTipo->close();

if (!isset($documentosPorTipo[$tipoProcesso])) {
  echo "Tipo de processo desconhecido.";
  exit();
}

// BUSCA DOCUMENTOS ENVIADOS
$sqlBusca = "SELECT DocumentoId, NomeDocumento, NomeArquivo, Status FROM documentos WHERE ProcId = ?";
$stmt = $conexao->prepare($sqlBusca);
$stmt->bind_param("i", $procId);
$stmt->execute();
$result = $stmt->get_result();
$documentosEnviados = [];
while ($row = $result->fetch_assoc()) {
  $documentosEnviados[$row['NomeDocumento']] = $row;
}
$stmt->close();

// PROCESSAMENTO DO UPLOAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
  $nomeDocumento = $_POST['nome_documento'];
  $arquivo = $_FILES['documento'];

  if ($arquivo['error'] === UPLOAD_ERR_OK) {
    $nomeOriginal = basename($arquivo['name']);
    $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
    $nomeUnico = uniqid('doc_', true) . '.' . $extensao;

    $nomeUsuarioSanitizado = preg_replace('/[^a-zA-Z0-9]/', '_', $nomeUsuario);
    $pastaBase = 'uploads/documentos/';
    $pastaUsuario = $pastaBase . $nomeUsuarioSanitizado . '_' . $usuarioId . '/';
    $pastaProcesso = $pastaUsuario . 'processo_' . $procId . '/';

    if (!is_dir($pastaProcesso)) {
      mkdir($pastaProcesso, 0777, true);
    }

    $caminhoCompleto = $pastaProcesso . $nomeUnico;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
      $sqlVerifica = "SELECT DocumentoId FROM documentos WHERE ProcId = ? AND NomeDocumento = ?";
      $stmtVerifica = $conexao->prepare($sqlVerifica);
      $stmtVerifica->bind_param("is", $procId, $nomeDocumento);
      $stmtVerifica->execute();
      $stmtVerifica->store_result();

      if ($stmtVerifica->num_rows > 0) {
        $stmtVerifica->bind_result($docId);
        $stmtVerifica->fetch();
        $sqlAtualiza = "UPDATE documentos SET NomeArquivo = ?, CaminhoArquivo = ?, Status = 'Em análise' WHERE DocumentoId = ?";
        $stmtAtualiza = $conexao->prepare($sqlAtualiza);
        $stmtAtualiza->bind_param("ssi", $nomeOriginal, $caminhoCompleto, $docId);
        $stmtAtualiza->execute();
      } else {
        $sqlInsere = "INSERT INTO documentos (ProcId, NomeDocumento, NomeArquivo, CaminhoArquivo, Status) VALUES (?, ?, ?, ?, 'Em análise')";
        $stmtInsere = $conexao->prepare($sqlInsere);
        $stmtInsere->bind_param("isss", $procId, $nomeDocumento, $nomeOriginal, $caminhoCompleto);
        $stmtInsere->execute();
      }

      header("Location: enviar_documentos.php?proc_id=$procId");
      exit();
    } else {
      echo "Erro ao mover o arquivo.";
    }
  } else {
    echo "Erro no upload do arquivo.";
  }
}
$usuarioNome = $_SESSION['usuario_nome'];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Envio de Documentos</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <link rel="stylesheet" href="styles/enviar_documentos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<header class="topo">
    <div class="container-header">
      <img src="img/logo.png" alt="Raízes Lusitanas" class="logo">
      <nav>
        <span class="boas-vindas">Bem-vindo, <?php echo htmlspecialchars($usuarioNome); ?></span>
        <a href="dashboard.php" class="btn-sair">Dashboard</a>
        <a href="scripts/logOff.php" class="btn-sair">Sair</a>
      </nav>
    </div>
  </header>

<body class="bg-light">
  <div class="container py-4">
    <h2 class="mb-4">Documentos Necessários - <?= htmlspecialchars($tipoProcesso) ?></h2>
    <div class="d-flex flex-wrap justify-content-center">

      <?php
      foreach ($documentosPorTipo[$tipoProcesso] as $docItem) {
        $nomeDoc = $docItem['nome'];
        $doc = $documentosEnviados[$nomeDoc] ?? null;
        $status = 'Não enviado';
        $classeStatus = 'secondary';
        $botao = '<br><button class="btn btn-primary" data-toggle="modal" data-target="#modalUpload" data-doc="' . $nomeDoc . '">Enviar</button>';

        if ($doc) {
          $status = $doc['Status'];
          switch ($status) {
            case 'Aprovado':
              $classeStatus = 'success';
              $botao = '';
              break;
            case 'Reprovado':
              $classeStatus = 'danger';
              $botao = '<br><button class="btn btn-warning" data-toggle="modal" data-target="#modalUpload" data-doc="' . htmlspecialchars($nomeDoc) . '">Reenviar</button>';
              break;
            case 'Em análise':
              $classeStatus = 'info';
              $botao = '';
              break;
            default:
              $classeStatus = 'secondary';
          }
        }
      
        echo '<div class="card m-2" style="width: 18rem;">
          <div class="card-body text-center">
            <i class="fas fa-' . htmlspecialchars($docItem['icone']) . ' fa-2x mb-2"></i>
            <h5 class="card-title">' . htmlspecialchars($nomeDoc) . '</h5>
            <p class="badge badge-' . $classeStatus . '">' . htmlspecialchars($status) . '</p>
            ' . $botao . '
          </div>
        </div>';
      }
      ?>
          </div>
        </div>
      
        <!-- Modal de Upload -->
        <div class="modal fade" id="modalUpload" tabindex="-1" role="dialog" aria-labelledby="modalUploadLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalUploadLabel">Enviar Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="nome_documento" id="nomeDocumentoInput">
                <div class="form-group">
                  <label for="documento">Selecionar arquivo:</label>
                  <input type="file" class="form-control-file" name="documento" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Enviar</button>
              </div>
            </form>
          </div>
        </div>
      
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
          $('#modalUpload').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var docName = button.data('doc');
            var modal = $(this);
            modal.find('#nomeDocumentoInput').val(docName);
          });
        </script>
      </body>
      
      </html>
