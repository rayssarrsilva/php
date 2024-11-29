<?php
include("valida.php"); // Verifica se o usuário está autenticado
include("conexao.php"); // Conexão com o banco de dados

// Variável para armazenar mensagem de erro ou sucesso
$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta o id do gênero a ser apagado
    $id_genero = $_POST["id_genero"];

    // Verificação se o id foi passado corretamente
    if (empty($id_genero)) {
        $erro = "ID do gênero não encontrado.";
    }

    // Se não houver erro, tenta apagar o gênero
    if (empty($erro)) {
        $sql = "DELETE FROM generos WHERE generos = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_genero);

        if ($stmt->execute()) {
            $sucesso = "Gênero excluído com sucesso!";
        } else {
            $erro = "Erro ao excluir o gênero: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Redireciona para a página de cadastro de gêneros após a exclusão
header("Location: cadastroGeneros.php?erro=" . urlencode($erro) . "&sucesso=" . urlencode($sucesso));
exit;
?>
