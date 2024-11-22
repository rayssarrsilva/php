<?php
include("conexao.php");

$cpf = $_POST["cpf"];

$sql = "delete from usuarios where cpf = ?";
$stmt = $conn->prepare($sql);

if ($stmt){
    $stmt->bind_param("s", $cpf);
    if(!$stmt->execute()){
        die("erro ao apagar");
    }
}else{
    die("Erro ao apagar");
}
header("Location: cadastroUsuarios.php");

?>