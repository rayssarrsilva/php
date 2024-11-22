<?php
include("conexao.php");

$cpf = $_POST["cpf"];
$nome = $_POST["nome"];
$senha = $_POST["senha"];
$cpfAnterior = $_POST["cpfAnterior"];

// Verifica se o CPF novo tem exatamente 11 dígitos
if (!preg_match('/^\d{11}$/', $cpf)) {
    echo "O CPF deve conter exatamente 11 dígitos.";
    exit;
}

// Verifica a senha (mínimo de 6 caracteres com uma letra maiúscula, uma minúscula, um número e um caractere especial)
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $senha)) {
    echo "A senha deve ter pelo menos 6 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.";
    exit;
}

// Verifique se o CPF anterior e o novo CPF são diferentes
if ($cpf != $cpfAnterior) {
    // Se o CPF foi alterado, verifique se o novo CPF já existe
    $checkSql = "SELECT COUNT(*) FROM usuarios WHERE cpf = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $cpf);
    $checkStmt->execute();
    $checkStmt->bind_result($cpfCount);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($cpfCount > 0) {
        echo "Este CPF já está em uso.";
        exit;
    }
}

// Verifique se o CPF anterior existe e se corresponde a um único usuário
$checkExistSql = "SELECT COUNT(*) FROM usuarios WHERE cpf = ?";
$checkExistStmt = $conn->prepare($checkExistSql);
$checkExistStmt->bind_param("s", $cpfAnterior);
$checkExistStmt->execute();
$checkExistStmt->bind_result($existCount);
$checkExistStmt->fetch();
$checkExistStmt->close();

if ($existCount == 0) {
    echo "Usuário com o CPF anterior não encontrado.";
    exit;
}

// Atualiza o usuário no banco de dados
$sql = "UPDATE usuarios SET cpf = ?, senha = ?, nome = ? WHERE cpf = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Vincula os parâmetros
    $stmt->bind_param("ssss", $cpf, $senha, $nome, $cpfAnterior);

    // Executa a consulta
    if ($stmt->execute()) {
        header("Location: cadastroUsuarios.php");
        exit; // Redireciona e para a execução do script
    } else {
        die("Erro ao atualizar o usuário.");
    }

    $stmt->close();
} else {
    die("Erro ao preparar a consulta.");
}

header("Location: cadastroUsuarios.php");
?>
