<?php
session_start();
require_once("scripts/conectaBanco.php");

if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit;
}

$usuarioNome = $_SESSION['usuario_nome'];
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Raízes Lusitanas</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  </head>

<body>
  <header class="topo">
    <div class="container-header">
      <img src="img/logo.png" alt="Raízes Lusitanas" class="logo">
      <nav>
        <span class="boas-vindas">Bem-vindo, <?php echo htmlspecialchars($usuarioNome); ?></span>
        <a href="editar_perfil.php" class="btn-sair">Editar Perfil</a>
        <a href="scripts/logOff.php" class="btn-sair">Sair</a>
      </nav>
    </div>
  </header>

  <main class="conteudo container py-4">
  <h1 class="titulo mb-4">Seus Processos</h1>
  <section class="processos row">
    <?php
    $usuarioId = $_SESSION['usuario_id'];
    $sql = "SELECT * FROM processos WHERE UsuId = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
      while ($proc = $resultado->fetch_assoc()) {
        $procId = $proc['ProcId'];
        $tipo = $proc['Tipo'];
        $etapaAtual = (int)$proc['EtapaAtual'];
        $porcentagem = intval($proc['Porcentagem'] ?? 0);

        // Definindo etapas manualmente com base no tipo
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
                  7 => "Aprovação e autorização de residência",
                  8 => "Agendamento da chegada do familiar",
                  9 => "Concluído"
              ];
              break;
          default:
              $etapas = [1 => "Etapa desconhecida"];
        }
        echo "<div class='card-processo col-md-5 m-2 p-3 border rounded shadow-sm'>";
        echo "<h2>" . htmlspecialchars($tipo) . "</h2>";
        echo "<p><strong>Status:</strong> " . htmlspecialchars($proc['Status']) . "</p>";
        echo "<p><strong>Data do Pedido:</strong> " . date("d/m/Y", strtotime($proc['DataPedido'])) . "</p>";
        echo "<p><strong>Última Atualização:</strong> " . date("d/m/Y H:i", strtotime($proc['UltimaAtualizacao'])) . "</p>";
        echo "<div class='barra-progresso bg-light rounded' style='height: 20px; overflow: hidden;'>";
        echo "  <div class='barra-preenchida' style='width: {$porcentagem}%; height: 100%;'></div>";
        echo "</div>";
        echo "<span class='texto-porcentagem'>{$porcentagem}%</span>";

        if (!empty($proc['Observacoes'])) {
          echo "<p><strong>Observações:</strong><br>" . nl2br(htmlspecialchars($proc['Observacoes'])) . "</p>";
        }
        echo "<br><button type='button' class='btn btn-primary btn-sm my-2' data-bs-toggle='modal' data-bs-target='#modalEtapas{$procId}'>Ver Etapas Concluídas</button>";
        echo "<a href='enviar_documentos.php?proc_id={$procId}' class='btn btn-success btn-sm ms-2'>Enviar Documentos</a>";
        echo "</div>";
        echo "
        <div class='modal fade' id='modalEtapas{$procId}' tabindex='-1' aria-labelledby='modalEtapasLabel{$procId}' aria-hidden='true'>
          <div class='modal-dialog'>
            <div class='modal-content'>
              <div class='modal-header'>
                <h5 class='modal-title' id='modalEtapasLabel{$procId}'>Etapas do Processo: " . htmlspecialchars($tipo) . "</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Fechar'></button>
              </div>
              <div class='modal-body'>
                <ul class='list-group'>";
                
                foreach ($etapas as $ordem => $desc) {
                  $icone = "❌";
                  if ($ordem < $etapaAtual) $icone = "✅";
                  else if ($ordem == $etapaAtual) $icone = "⏳";

                  echo "<li class='list-group-item d-flex justify-content-between align-items-center'>" . htmlspecialchars($desc) . "<span>$icone</span></li>";
                }
        echo "    </ul>
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fechar</button>
              </div>
            </div>
          </div>
        </div>";
      }
    } else {
      echo "<p class='sem-processos'>Você ainda não possui processos registrados.</p>";
    }
    ?>
  </section>
</main>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>

</html>
