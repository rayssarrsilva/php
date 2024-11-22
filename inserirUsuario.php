<?php
include("conexao.php");

$cpf = $_POST["cpf"];
$nome = $_POST["nome"];
$senha = $_POST["senha"];

// Validação do CPF usando regex (apenas números, 11 dígitos)
if (!preg_match("/^\d{11}$/", $cpf)) {
    die("CPF inválido. O CPF deve conter apenas 11 dígitos.");
}

// Validação de senha (mínimo de 8 caracteres, ao menos 1 letra maiúscula, 1 minúscula e 1 número)
if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/", $senha)) {
    die("Senha inválida. A senha deve ter pelo menos 8 caracteres, incluindo letras maiúsculas, minúsculas e números.");
}

// Preparando a consulta SQL com prepared statement para evitar SQL Injection
$stmt = $conn->prepare("INSERT INTO usuarios (cpf, nome, senha) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $cpf, $nome, $senha); // 'sss' indica que todos os parâmetros são strings

// Executando a consulta
if ($stmt->execute()) {
    // Redireciona após sucesso
    header("Location: cadastroUsuarios.php");
} else {
    // Se ocorrer um erro, exibe uma mensagem
    die("Erro ao cadastrar usuário: " . $stmt->error);
}

// Fechando a conexão com o banco
$stmt->close();
$conn->close();
?>
