<?php
require_once 'scripts/conectaBanco.php';
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != true) {
    header("Location: login.php");
    exit;
}

$usuId = $_GET['id'] ?? null;

if (!$usuId) {
    echo "ID do usuário inválido.";
    exit;
}

$stmt = $conexao->prepare("SELECT NomeArquivo, CaminhoArquivo, DataEnvio FROM documentos WHERE  = ?");
$stmt->bind_param("i", $usuId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/admin.css" />
    <link rel="stylesheet" href="styles/detalhes_cliente.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <title>Documentos Enviados</title>
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

<h2>Documentos Enviados</h2>

<?php if ($result->num_rows > 0): ?>
    <ul>
        <?php while ($doc = $result->fetch_assoc()): ?>
            <li>
                <strong><?= htmlspecialchars($doc['NomeArquivo']) ?></strong> -
                Enviado em: <?= $doc['DataEnvio'] ?> -
                <a href="<?= $doc['CaminhoArquivo'] ?>" target="_blank">Ver Documento</a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>Nenhum documento enviado.</p>
<?php endif; ?>
<a href="admin.php" class="voltar">← Voltar ao Painel</a>
</div>

</body>
</html>

