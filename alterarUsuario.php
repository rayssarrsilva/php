<?php
include("conexao.php");

$cpf = $_POST["cpf"];
$nome = $_POST["nome"];
$senha = $_POST["senha"];
$cpfAnterior = $_POST["cpfAnterior"];

if (!preg_match('/^\d{11}$/', $cpf)) {
    echo "O CPF deve conter exatamente 11 dígitos.";
    exit;
} // verifica se tem no minimo 11 digitos

// Verificação da senha usando regex (pode ser feito em JavaScript antes do envio)
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $senha)) {
    echo "A senha deve ter pelo menos 6 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.";
    exit;
}

$sql = "update usuarios set cpf = '$cpf',
                            senha = '$senha',
                            nome = '$nome'
        where cpf= '$cpfAnterior'";

if(!$resultado = $conn->query($sql)){
    die("erro");
}
header("Location: cadastroUsuarios.php");
