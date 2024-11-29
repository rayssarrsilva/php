<?php
include("valida.php"); // Verifica se o usuário está autenticado
include("conexao.php"); // Conexão com o banco de dados

// Variáveis para armazenar erros e mensagens de sucesso
$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $id_genero = $_POST["id_genero"];
    $descricao = $_POST["descricao"];
    $status = $_POST["status"];

    // Verificação de erros
    if (empty($descricao)) {
        $erro = "A descrição do gênero é obrigatória.";
    }

    // Se não houver erro, atualiza o gênero no banco
    if (empty($erro)) {
        $sql = "UPDATE generos SET descricao = ?, status = ? WHERE generos = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $descricao, $status, $id_genero);

        if ($stmt->execute()) {
            $sucesso = "Gênero alterado com sucesso!";
        } else {
            $erro = "Erro ao alterar o gênero: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<html>
<head>
    <title>Alterar Gênero</title>
</head>
<body>

<div style="width: 800px; margin: 0 auto;">
    <div style="min-height: 100px; width: 100%; background-color: #4CAF50;">
        <div style="width: 50%; float:left">
            <span style="padding-left: 10px;">Olá <?=$_SESSION['nome'];?></span>
        </div>

        <div style="width: 50%; float:left; text-align:right;">
            <span style="background-color:blue; margin-right:10px;"> <a href="sair.php"><font color="black">SAIR</font></a></span>
        </div>
    </div>
    
    <div id="menu" style="width: 200px; background-color: #f4f4f4; min-height: 400px; float: left;">
        <h2>Menu</h2>
        <p><a href="cadastroUsuarios.php"><font color="black">Cadastrar Usuários</font></a></p>
        <p><a href="cadastroGeneros.php"><font color="black">Cadastrar Gêneros</font></a></p>
    </div>

    <div style="background-color: #ddd; min-height: 400px; width: 600px; float:left">
        <h2>Alterar Gênero</h2>
        
        <?php if ($erro != "") { ?>
            <!-- Exibe o erro -->
            <div style="color: red;"><?= $erro; ?></div>
        <?php } ?>

        <?php if ($sucesso != "") { ?>
            <!-- Exibe a mensagem de sucesso -->
            <div style="color: green;"><?= $sucesso; ?></div>
        <?php } ?>

        <!-- Formulário para editar gênero -->
        <form method="post" action="alterarGeneros.php">
            DESCRIÇÃO: <input type="text" name="descricao" value="<?= isset($descricao) ? $descricao : ''; ?>"><br>
            STATUS:
            <select name="status">
                <option value="1" <?= isset($status) && $status == 1 ? 'selected' : ''; ?>>Ativo</option>
                <option value="0" <?= isset($status) && $status == 0 ? 'selected' : ''; ?>>Inativo</option>
            </select><br>
            <input type="hidden" name="id_genero" value="<?= isset($id_genero) ? $id_genero : ''; ?>">
            <input type="submit" value="Alterar">
        </form>

    </div>
</div>

</body>
</html>
