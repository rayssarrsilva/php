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

$sql = "update usuarios set cpf = ?,
                            senha = ?,
                            nome = ?
        where cpf= ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Vincular parâmetros
        $stmt->bind_param("ssss", $cpf, $senha, $nome, $cpfAnterior);

        // Executar a consulta
        if ($stmt->execute()) {
            header("Location: cadastroUsuarios.php");
            exit; // Adiciona exit após o redirecionamento
        } else {
            die("Erro ao atualizar o usuário.");
        }

        $stmt->close();
    } else {
        die("Erro ao preparar a consulta.");
    }
?>