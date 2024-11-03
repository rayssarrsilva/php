<?php
include("conexao.php");

$cpf = $_POST["cpf"];
$senha = $_POST["senha"];

if (!preg_match('/^\d{11}$/', $cpf)) {
    echo "O CPF deve conter exatamente 11 dígitos.";
    exit;
} // verifica se tem no minimo 11 digitos

// Verificação da senha usando regex (pode ser feito em JavaScript antes do envio)
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $senha)) {
    echo "A senha deve ter pelo menos 6 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.";
    exit;
}

$sql = "SELECT nome FROM usuarios WHERE cpf=? AND senha=?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ss", $cpf, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['nome'] != '') {
            session_start();
            $_SESSION["cpf"] = $cpf;
            $_SESSION["senha"] = $senha;
            $_SESSION["nome"] = $row['nome'];
            header("Location: principal.php"); 
        }
    } else {
        echo "Senha incorreta.";
    }
}
?>

