<?php
session_start();
require_once("scripts/conectaBanco.php");

// Verificação de login (opcional, para segurança extra)
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['admin'] != true) {
    header("Location: login.php");
    exit;
}

$tipoFiltro = $_GET['tipo_processo'] ?? '';
$busca = $_GET['busca'] ?? '';

$sql = "SELECT u.Nome, u.Email, p.Tipo, p.Status, p.DataPedido, p.UltimaAtualizacao, p.Porcentagem, p.ProcId
        FROM usuarios u
        INNER JOIN processos p ON u.UsuId = p.UsuId
        WHERE p.Status IS NOT NULL";

if (!empty($tipoFiltro)) {
    $sql .= " AND p.Tipo = '" . $conexao->real_escape_string($tipoFiltro) . "'";
}

if (!empty($busca)) {
    $busca = $conexao->real_escape_string($busca);
    $sql .= " AND (u.Nome LIKE '%$busca%' OR p.ProcId LIKE '%$busca%')";
}

$sql .= " ORDER BY p.UltimaAtualizacao DESC";
$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administração - Raízes Lusitanas</title>
    <link rel="stylesheet" href="styles/admin.css" />
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

    <main class="conteudo">
        <h1 class="titulo">Usuários e Processos</h1>
        <form method="GET" class="filtros-admin">
  <input type="text" name="busca" placeholder="Buscar por nome ou codigo" value="<?= htmlspecialchars($busca) ?>">

  <select name="tipo_processo">
    <option value="">Todos os tipos</option>
    <option value="Visto de Trabalho" <?= $tipoFiltro === 'Visto de Trabalho' ? 'selected' : '' ?>>Visto de Trabalho</option>
    <option value="Cidadania Portuguesa" <?= $tipoFiltro === 'Cidadania Portuguesa' ? 'selected' : '' ?>>Cidadania Portuguesa</option>
    <option value="Nacionalidade" <?= $tipoFiltro === 'Nacionalidade' ? 'selected' : '' ?>>Nacionalidade</option>
  </select>

  <button type="submit">Filtrar</button>
  <a href="admin.php" class="btn-limpar">Limpar Filtros</a>
  <a href="scripts/exporta_excel.php?tipo_processo=<?= urlencode($tipoFiltro) ?>&busca=<?= urlencode($busca) ?>" class="btn-exportar">Exportar Excel</a>
</form>


        <div class="tabela-admin">
            <table>
                <thead>
                    <tr>
                    <th>Codigo</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo de Processo</th>
                        <th>Status</th>
                        <th>Início</th>
                        <th>Última Atualização</th>
                        <th>Progresso (%)</th> <!-- Nova coluna -->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ProcId']) ?></td>
                            <td><?= htmlspecialchars($row['Nome']) ?></td>
                            <td><?= htmlspecialchars($row['Email']) ?></td>
                            <td><?= htmlspecialchars($row['Tipo'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['Status'] ?? '-') ?></td>
                            <td><?= $row['DataPedido'] ? date('d/m/Y', strtotime($row['DataPedido'])) : '-' ?></td>
                            <td><?= $row['UltimaAtualizacao'] ? date('d/m/Y', strtotime($row['UltimaAtualizacao'])) : '-' ?></td>
                            <td>
                                <?php if (isset($row['Porcentagem'])): ?>
                                    <div class="barra-progresso">
                                        <div class="barra-preenchida" style="width: <?= intval($row['Porcentagem']) ?>%;"></div>
                                    </div>
                                    <span class="texto-porcentagem"><?= intval($row['Porcentagem']) ?>%</span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>