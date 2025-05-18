
<?php
session_start();
require_once("conectaBanco.php");

// Função para limpar entradas
function limpar($valor) {
    return htmlspecialchars(trim($valor));
}

$token = $_POST['token'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

if (!$token || !$nova_senha || !$confirmar_senha) {
    $_SESSION['erro_redefinir'] = "Preencha todos os campos.";
    header("Location: ../redefinir_senha.php?token=" . urlencode($token));
    exit;
}

if ($nova_senha !== $confirmar_senha) {
    $_SESSION['erro_redefinir'] = "As senhas não conferem.";
    header("Location: ../redefinir_senha.php?token=" . urlencode($token));
    exit;
}

// Verifica se o token é válido e não expirou
$stmt = $conexao->prepare("SELECT UsuId, token_expira FROM usuarios WHERE token_recuperacao = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['erro_redefinir'] = "Token inválido.";
    header("Location: ../login.php");
    exit;
}

$usuario = $result->fetch_assoc();

if (strtotime($usuario['token_expira']) < time()) {
    $_SESSION['mensagem_login'] = ['tipo' => 'erro', 'texto' => 'Token Expirado.'];
    header("Location: ../login.php");
    exit;
}

// Atualiza a senha e limpa o token
$senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

$stmt = $conexao->prepare("UPDATE usuarios SET Senha = ?, token_recuperacao = NULL, token_expira = NULL WHERE UsuId = ?");
$stmt->bind_param("si", $senha_hash, $usuario['UsuId']);

if ($stmt->execute()) {
    $_SESSION['mensagem_login'] = ['tipo' => 'sucesso', 'texto' => 'Senha alterada com sucesso! Faça login.'];
    header("Location: ../login.php");
    exit;
} else {
    $_SESSION['mensagem_login'] = ['tipo' => 'erro', 'texto' => 'Erro ao alterar senha. Tente novamente.'];
    header("Location: ../redefinir_senha.php?token=" . urlencode($token));
    exit;
}
?>

