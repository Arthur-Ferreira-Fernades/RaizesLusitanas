<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("conectaBanco.php");

$docId = $_POST['docId'] ?? null;
$acao = $_POST['acao'] ?? null;
$observacao = $_POST['observacao'] ?? '';

if (!$docId || !$acao) {
    die("Parâmetros obrigatórios ausentes.");
}

// Buscar dados do documento, processo e cliente
$query = "SELECT d.NomeArquivo, d.NomeDocumento, p.UsuId, u.Nome AS NomeCliente
          FROM documentos d
          JOIN processos p ON d.ProcId = p.ProcId
          JOIN usuarios u ON p.UsuId = u.UsuId
          WHERE d.DocumentoId = ?";
$stmt = mysqli_prepare($conexao, $query);
if (!$stmt) {
    die("Erro ao preparar statement: " . mysqli_error($conexao));
}
mysqli_stmt_bind_param($stmt, "i", $docId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$dados = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$dados) {
    die("Documento não encontrado.");
}

$arquivoAtual = $dados['NomeArquivo'];
$NomeDocumento = $dados['NomeDocumento'];
$clienteId = $dados['UsuId'];
$nomeCliente = preg_replace('/[^a-zA-Z0-9-_]/', '_', strtolower($dados['NomeCliente'])); // sanitiza nome

$caminhoArquivo = "uploads/clientes/$clienteId/$arquivoAtual";

if ($acao === 'aprovar') {
    // Renomear arquivo
    $extensao = pathinfo($arquivoAtual, PATHINFO_EXTENSION);
    $novoNomeArquivo = "{$nomeCliente}_{$NomeDocumento}." . $extensao;
    $novoCaminho = "uploads/clientes/$clienteId/$novoNomeArquivo";

    if (!rename($caminhoArquivo, $novoCaminho)) {
        die("Erro ao renomear o arquivo.");
    }

    // Atualizar no banco
    $query = "UPDATE documentos SET Status = 'aprovado', Observacao = NULL, NomeArquivo = ? WHERE DocumentoId = ?";
    $stmt = mysqli_prepare($conexao, $query);
    if (!$stmt) {
        die("Erro ao preparar update: " . mysqli_error($conexao));
    }
    mysqli_stmt_bind_param($stmt, "si", $novoNomeArquivo, $docId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

} elseif ($acao === 'reprovar') {
    // Excluir arquivo
    if (file_exists($caminhoArquivo)) {
        unlink($caminhoArquivo);
    }

    // Atualizar status e observação no banco
    $query = "UPDATE documentos SET Status = 'reprovado', Observacao = ? WHERE DocumentoId = ?";
    $stmt = mysqli_prepare($conexao, $query);
    if (!$stmt) {
        die("Erro ao preparar update: " . mysqli_error($conexao));
    }
    mysqli_stmt_bind_param($stmt, "si", $observacao, $docId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    die("Ação inválida.");
}

// Redireciona de volta
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
