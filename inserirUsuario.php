<?php
include("conexao.php");

$cpf = $_POST["cpf"];
$nome = $_POST["nome"];
$senha = $_POST["senha"];

$sql = "insert into usuarios (cpf,nome,senha) values('$cpf','$nome','$senha') ";
if(!$resultado = $conn->query($sql)){
    die("erro");
}
header("Location: cadastroUsuarios.php");