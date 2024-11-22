<?php
include("valida.php"); // Verifica se o usuário está autenticado
include("conexao.php"); // Conexão com o banco de dados

// Variável para armazenar mensagem de erro ou sucesso
$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta o id do filme a ser apagado
    $id_filme = $_POST["id_filme"];

    // Verificação se o id foi passado corretamente
    if (empty($id_filme)) {
        $erro = "ID do filme não encontrado.";
    }

    // Se não houver erro, tenta apagar o filme
    if (empty($erro)) {
        $sql = "DELETE FROM filmes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_filme);

        if ($stmt->execute()) {
            $sucesso = "Filme excluído com sucesso!";
        } else {
            $erro = "Erro ao excluir o filme: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Redireciona para a página de cadastro de filmes após a exclusão
header("Location: cadastroFilmes.php?erro=" . urlencode($erro) . "&sucesso=" . urlencode($sucesso));
exit;
?>
