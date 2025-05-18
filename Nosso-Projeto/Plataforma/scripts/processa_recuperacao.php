<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/processa_recuperacao.css">
    <title>Recuperacao de senha</title>
</head>
<body>
    
</body>
</html>
<?php
session_start();
require_once("conectaBanco.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['recuperacao_erro'] = "E-mail inválido.";
        header("Location: ../recupera_senha.php");
        exit;
    }

    // Verifica se o e-mail existe
    $stmt = $conexao->prepare("SELECT UsuId, Nome FROM usuarios WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $token = bin2hex(random_bytes(32));
        $expiracao = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Armazena o token temporariamente no banco
        $stmtToken = $conexao->prepare("UPDATE usuarios SET token_recuperacao = ?, token_expira = ? WHERE UsuId = ?");
        $stmtToken->bind_param("ssi", $token, $expiracao, $usuario["UsuId"]);
        $stmtToken->execute();

        // Simula envio de e-mail (você pode implementar envio real com PHPMailer ou mail())
        $link = "http://localhost/Nosso-Projeto/Plataforma/redefinir_senha.php?token=" . $token;
        echo "<body class='login-bg'>
    <div class='container d-flex align-items-center justify-content-center vh-100'>
        <div class='card shadow p-5 rounded-4 login-card'>";
        echo "<h2>Link de redefinição de senha enviado para: $email</h2>";
        echo "<p><a href='$link'>Clique aqui para redefinir a senha</a></p>";
        echo "<p><small>Este link expira em 1 hora.</small></p>";
        echo " </div>
    </div>
</body>";

    } else {
        $_SESSION['recuperacao_erro'] = "E-mail não encontrado.";
        header("Location: ../recupera_senha.php");
        exit;
    }
}
?>
