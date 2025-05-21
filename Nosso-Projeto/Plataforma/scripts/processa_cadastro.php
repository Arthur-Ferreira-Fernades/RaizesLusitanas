<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'conectaBanco.php';

$camposObrigatorios = ['nome', 'email', 'telefone', 'senha', 'nascimento', 'nacionalidade', 'estado_civil', 'endereco', 'cidade', 'estado', 'pais', 'cpf', 'rg', 'Tipo'];
foreach ($camposObrigatorios as $campo) {
  if (empty($_POST[$campo])) {
    $_SESSION['cadastro_erro'] = "Preencha todos os campos obrigatórios.";
    header("Location: ../cadastro.php");
    exit;
  }
}

// Sanitização dos dados
$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$telefone = trim($_POST['telefone']);
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
$nascimento = $_POST['nascimento'];
$nacionalidade = trim($_POST['nacionalidade']);
$estado_civil = trim($_POST['estado_civil']);
$endereco = trim($_POST['endereco']);
$cidade = trim($_POST['cidade']);
$estado = trim($_POST['estado']);
$pais = trim($_POST['pais']);
$cpf = trim($_POST['cpf']);
$rg = trim($_POST['rg']);
$admin = ($_POST['Tipo'] == 'admin') ? 'admin' : 'cliente';

// Verifica se e-mail já existe
$sql_verifica = "SELECT UsuId FROM usuarios WHERE cpf = ?";
$stmt_verifica = $conexao->prepare($sql_verifica);
$stmt_verifica->bind_param("s", $cpf);
$stmt_verifica->execute();
$stmt_verifica->store_result();

if ($stmt_verifica->num_rows > 0) {
  $_SESSION['cadastro_erro'] = "Este usuario já está cadastrado.";
  header("Location: ../cadastro.php");
  exit;
}

// Inserção no banco
$sql = "INSERT INTO usuarios (Nome, Email, Telefone, Senha, nascimento, nacionalidade, estado_civil, endereco, cidade, estado, pais, cpf, rg, Tipo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ssssssssssssss", $nome, $email, $telefone, $senha, $nascimento, $nacionalidade, $estado_civil, $endereco, $cidade, $estado, $pais, $cpf, $rg, $admin);

if ($stmt->execute()) {
  $_SESSION['cadastro_sucesso'] = "Conta criada com sucesso!";
} else {
  $_SESSION['cadastro_erro'] = "Erro ao cadastrar: " . $stmt->error;
}

$stmt->close();
header("Location: ../admin.php");
exit;
?>
