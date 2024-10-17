<?php
include("conexao.php");

$cpf=$_POST["cpf"];
$senha=$_POST["senha"];

$sql = "select nome from usuarios where cpf=? and senha=?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ss", $cpf, $senha);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $resultado = $conn->query($sql);
    $row = $resultado->fetch_assoc();

if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    if($row['nome'] != ''){
        session_start();
        $_SESSION["cpf"] = $cpf;
        $_SESSION["senha"] = $senha;
        $_SESSION["nome"] = $row['nome'];
        header("Location: principal.php"); 
    }
}else{
    echo "senha incorreta";
    }
}
?>
