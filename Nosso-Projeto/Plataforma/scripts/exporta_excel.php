<?php
require_once("conectaBanco.php");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=relatorio_processos.xls");
header("Pragma: no-cache");
header("Expires: 0");

$tipo = $_GET['tipo_processo'] ?? '';
$busca = $_GET['busca'] ?? '';

$sql = "SELECT u.Nome, u.Email, p.Tipo, p.Status, p.DataPedido, p.UltimaAtualizacao, p.Porcentagem
        FROM usuarios u
        INNER JOIN processos p ON u.UsuId = p.UsuId
        WHERE p.Status IS NOT NULL";

if (!empty($tipo)) {
    $sql .= " AND p.Tipo = '" . $conexao->real_escape_string($tipo) . "'";
}

if (!empty($busca)) {
    $busca = $conexao->real_escape_string($busca);
    $sql .= " AND (u.Nome LIKE '%$busca%' OR u.Email LIKE '%$busca%')";
}

$sql .= " ORDER BY p.UltimaAtualizacao DESC";

$result = $conexao->query($sql);

// Cabeçalho da tabela
echo "<table border='1'>";
echo "<tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Tipo de Processo</th>
        <th>Status</th>
        <th>Data de Início</th>
        <th>Última Atualização</th>
        <th>Porcentagem</th>
      </tr>";

// Dados
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . htmlspecialchars($row['Nome']) . "</td>
            <td>" . htmlspecialchars($row['Email']) . "</td>
            <td>" . htmlspecialchars($row['Tipo']) . "</td>
            <td>" . htmlspecialchars($row['Status']) . "</td>
            <td>" . ($row['DataPedido'] ? date('d/m/Y', strtotime($row['DataPedido'])) : '-') . "</td>
            <td>" . ($row['UltimaAtualizacao'] ? date('d/m/Y', strtotime($row['UltimaAtualizacao'])) : '-') . "</td>
            <td>" . $row['Porcentagem'] . "%</td>
          </tr>";
}
echo "</table>";
?>
