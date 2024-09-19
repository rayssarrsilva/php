<?php
include("conexao.php");

$cpf = $_POST["cpf"];
$nome = $_POST["nome"];
$senha = $_POST["senha"];
$cpfAnterior = $_POST["cpfAnterior"];

$sql = "update usuarios set cpf = '$cpf',
                            senha = '$senha',
                            nome = '$nome'
        where cpf= '$cpfAnterior'";

if(!$resultado = $conn->query($sql)){
    die("erro");
}
header("Location: cadastroUsuarios.php");