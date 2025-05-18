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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <header class="topo">
    <div class="container-header">
      <img src="img/logo.png" alt="Raízes Lusitanas" class="logo">
      <nav>
        <span class="boas-vindas">Bem-vindo, <?php echo htmlspecialchars($usuarioNome); ?></span>
        <a href="scripts/logOff.php" class="btn-sair">Sair</a>
      </nav>
    </div>
  </header>

  <main class="conteudo">
    <h1 class="titulo">Seus Processos</h1>
    <section class="processos">
      <?php
      $usuarioId = $_SESSION['usuario_id'];
      $sql = "SELECT * FROM processos WHERE UsuId = ?";
      $stmt = $conexao->prepare($sql);
      $stmt->bind_param("i", $usuarioId);
      $stmt->execute();
      $resultado = $stmt->get_result();

      if ($resultado->num_rows > 0) {
        while ($proc = $resultado->fetch_assoc()) {
          $porcentagem = intval($proc['Porcentagem'] ?? 0); // Pega a porcentagem ou 0 se não existir
          echo "<div class='card-processo'>";
          echo "<h2>" . htmlspecialchars($proc['Tipo']) . "</h2>";
          echo "<p><strong>Status:</strong> " . htmlspecialchars($proc['Status']) . "</p>";
          echo "<p><strong>Data do Pedido:</strong> " . date("d/m/Y", strtotime($proc['DataPedido'])) . "</p>";
          echo "<p><strong>Última Atualização:</strong> " . date("d/m/Y H:i", strtotime($proc['UltimaAtualizacao'])) . "</p>";
          
          // Barra de porcentagem
          echo "<div class='barra-progresso'>";
          echo "  <div class='barra-preenchida' style='width: {$porcentagem}%;'></div>";
          echo "</div>";
          echo "<span class='texto-porcentagem'>{$porcentagem}%</span>";
        
          if ($proc['Observacoes'] != null) {
            echo "<p><strong>Observações:</strong><br>" . nl2br(htmlspecialchars($proc['Observacoes'])) . "</p>";
            echo "</div>";
          }
          
        }
      } else {
        echo "<p class='sem-processos'>Você ainda não possui processos registrados.</p>";
      }
      ?>
    </section>
  </main>
</body>
</html>
