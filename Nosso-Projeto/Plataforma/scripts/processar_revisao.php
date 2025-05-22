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

// Busca dados do documento
$query = "SELECT d.NomeArquivo, d.CaminhoArquivo, d.NomeDocumento 
          FROM documentos d
          JOIN processos p ON d.ProcId = p.ProcId
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

// Construção ABSOLUTA do caminho
$basePath = $_SERVER['DOCUMENT_ROOT'] . '/Nosso-Projeto/Plataforma/'; // Ajuste conforme sua estrutura
$caminhoArquivo = $basePath . $dados['CaminhoArquivo'];
$diretorio = dirname($caminhoArquivo);

// Verificações reforçadas
if (!file_exists($caminhoArquivo)) {
    die("Arquivo não encontrado: " . $caminhoArquivo);
}

if (!is_dir($diretorio)) {
    die("Diretório não existe: " . $diretorio);
}

if ($acao === 'aprovar') {
    // Verificação de permissão com caminho absoluto
    if (!is_writable($diretorio)) {
        die("Diretório sem permissão (caminho real: $diretorio)");
    }

    // Sanitização do nome
    $nomeDocumento = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($dados['NomeDocumento']));
    $extensao = pathinfo($dados['NomeArquivo'], PATHINFO_EXTENSION);
    $novoNomeArquivo = "documento_aprovado_{$nomeDocumento}.{$extensao}";
    $novoCaminho = $diretorio . DIRECTORY_SEPARATOR . $novoNomeArquivo;

    // Operação de rename com tratamento de erro
    if (!rename($caminhoArquivo, $novoCaminho)) {
        error_log("Falha ao renomear: " . print_r(error_get_last(), true));
        die("Erro interno ao processar arquivo. Verifique logs.");
    }

    // Atualização do banco de dados
    $caminhoRelativo = str_replace($basePath, '', $novoCaminho); // Mantém caminho relativo
    $query = "UPDATE documentos SET 
              Status = 'aprovado', 
              Observacao = NULL, 
              NomeArquivo = ?,
              CaminhoArquivo = ? 
              WHERE DocumentoId = ?";
    
    $stmt = mysqli_prepare($conexao, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $novoNomeArquivo, $caminhoRelativo, $docId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

} elseif ($acao === 'reprovar') {
    // Exclusão com verificação
    if (!unlink($caminhoArquivo)) {
        error_log("Falha ao excluir: " . print_r(error_get_last(), true));
        die("Erro ao remover arquivo físico.");
    }

    $query = "UPDATE documentos SET 
              Status = 'reprovado', 
              Observacao = ? 
              WHERE DocumentoId = ?";
    $stmt = mysqli_prepare($conexao, $query);
    mysqli_stmt_bind_param($stmt, "si", $observacao, $docId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;