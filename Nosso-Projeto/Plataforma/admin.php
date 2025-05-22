<?php
session_start();
require_once("scripts/conectaBanco.php");

// Verificação de login (opcional, para segurança extra)
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['admin'] != true) {
    header("Location: login.php");
    exit;
}

$ordenarPor = $_GET['ordenar_por'] ?? 'p.ProcId';
$ordem = $_GET['ordem'] ?? 'ASC';

// Lista de colunas permitidas para ordenação
$colunasPermitidas = [
    'ProcId' => 'p.ProcId',
    'Nome' => 'u.Nome',
    'Tipo' => 'p.Tipo',
    'DataPedido' => 'p.DataPedido'
];

// Valida coluna e aplica
$colunaOrdenacao = $colunasPermitidas[$ordenarPor] ?? 'p.ProcId';
$ordemSQL = ($ordem === 'DESC') ? 'DESC' : 'ASC';


$tipoFiltro = $_GET['tipo_processo'] ?? '';
$busca = $_GET['busca'] ?? '';
$somenteConcluidos = isset($_GET['somente_concluidos']);

$sql = "SELECT u.UsuId, u.Nome, u.Email, p.Tipo, p.Status, p.DataPedido, p.UltimaAtualizacao, p.Porcentagem, p.ProcId
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

if ($somenteConcluidos) {
    $sql .= " AND p.Status = 'Concluído'";
}

// Ordenação: se não estiver filtrando por concluídos, ordene para deixar concluídos por último
$sql .= " ORDER BY $colunaOrdenacao $ordemSQL";

$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administração - Raízes Lusitanas</title>
    <link rel="stylesheet" href="styles/admin.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

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
    <?php if (isset($_SESSION['cadastro_erro'])): ?>
      <div class="alert alert-danger p-2"><?php echo $_SESSION['cadastro_erro']; unset($_SESSION['cadastro_erro']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['cadastro_sucesso'])): ?>
      <div class="alert alert-success p-2"><?php echo $_SESSION['cadastro_sucesso']; unset($_SESSION['cadastro_sucesso']); ?></div>
    <?php endif; ?>
    <main class="conteudo">
        <h1 class="titulo">Usuários e Processos</h1>
        <form method="GET" class="filtros-admin">
            <input type="text" name="busca" placeholder="Buscar por nome ou codigo" value="<?= htmlspecialchars($busca) ?>">

            <select name="tipo_processo">
                <option value="">Todos os tipos</option>
                <option value="Visto de Residência" <?= $tipoFiltro === 'Visto de Residência' ? 'selected' : '' ?>>Visto de Residência</option>
                <option value="Cidadania Portuguesa" <?= $tipoFiltro === 'Cidadania Portuguesa' ? 'selected' : '' ?>>Cidadania Portuguesa</option>
                <option value="Nacionalidade Por Descendencia" <?= $tipoFiltro === 'Nacionalidade Por Descendencia' ? 'selected' : '' ?>>Nacionalidade Por Descendencia</option>
                <option value="Nacionalidade Por Casamento" <?= $tipoFiltro === 'Nacionalidade Por Casamento' ? 'selected' : '' ?>>Nacionalidade Por Casamento</option>
                <option value="Nacionalidade Por Tempo de Residencia" <?= $tipoFiltro === 'Nacionalidade Por Tempo de Residencia' ? 'selected' : '' ?>>Nacionalidade Por Tempo de Residencia</option>
                <option value="Reagrupamento Familiar" <?= $tipoFiltro === 'Reagrupamento Familiar' ? 'selected' : '' ?>>Reagrupamento Familiar</option>
            </select>

            <label class="checkbox">
                <input type="checkbox" name="somente_concluidos" <?= isset($_GET['somente_concluidos']) ? 'checked' : '' ?>>
                Somente Concluídos
            </label>

            <button type="submit" class="btn-limpar">Filtrar</button>
        </form>

        <div class="botoes-admin">
            <a href="admin.php" class="btn-acao verde">Limpar Filtros</a>
            <a href="scripts/exporta_excel.php?tipo_processo=<?= urlencode($tipoFiltro) ?>&busca=<?= urlencode($busca) ?>" class="btn-acao verde">Exportar Excel</a>
            <a href="criar_processo.php" class="btn-acao verde">+ Criar Novo Processo</a>
            <a href="cadastro.php" class="btn-acao verde">Cadastrar Cliente</a>
        </div>

        <div class="tabela-admin">
            <table>
                <thead>
                    <?php
                    // Função auxiliar para alternar ordem
                    function urlOrdenacao($coluna)
                    {
                        $params = $_GET;
                        $ordemAtual = $_GET['ordem'] ?? 'ASC';
                        $colunaAtual = $_GET['ordenar_por'] ?? '';

                        // Alterna ordem se a coluna for a mesma
                        if ($colunaAtual === $coluna) {
                            $params['ordem'] = $ordemAtual === 'ASC' ? 'DESC' : 'ASC';
                        } else {
                            $params['ordem'] = 'ASC';
                        }

                        $params['ordenar_por'] = $coluna;

                        return '?' . http_build_query($params);
                    }

                    function iconeOrdenacao($coluna)
                    {
                        $ordemAtual = $_GET['ordem'] ?? 'ASC';

                            return $ordemAtual === 'ASC' ? '↑' : '↓';
                    }

                    ?>

                    <tr>
                        <th><a href="<?= urlOrdenacao('ProcId') ?>">Código <?= iconeOrdenacao('ProcId') ?></a></th>
                        <th><a href="<?= urlOrdenacao('Nome') ?>">Nome <?= iconeOrdenacao('Nome') ?></a></th>
                        <th>Email</th>
                        <th><a href="<?= urlOrdenacao('Tipo') ?>">Tipo de Processo <?= iconeOrdenacao('Tipo') ?></a></th>
                        <th>Status</th>
                        <th><a href="<?= urlOrdenacao('DataPedido') ?>">Início <?= iconeOrdenacao('DataPedido') ?></a></th>
                        <th>Última Atualização</th>
                        <th>Progresso (%)</th>
                        <th>Ações</th>
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

                                <div class="barra-progresso">
                                    <div class="barra-preenchida" style="width: <?= intval($row['Porcentagem']) ?>%;"></div>
                                </div>
                                <span class="texto-porcentagem"><?= intval($row['Porcentagem']) ?>%</span>
                            </td>
                            <td>
                                <div class="grupo-acoes">
                                    <a href="atualiza_status.php?proc_id=<?= htmlspecialchars($row['ProcId']) ?>" class="btn-acao azul">Atualizar</a>
                                    <a href="detalhes_cliente.php?proc_id=<?= htmlspecialchars($row['ProcId']) ?>" class="btn-acao verde">Ver Detalhes</a>                                    <a href="revisar_documentos.php?usuId=<?= $row['UsuId'] ?>&processoId=<?= $row['ProcId'] ?>" class="btn btn-primary btn-sm">Revisar Documentos</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>