<?php
include("valida.php"); // Verifica se o usuário está autenticado
include("conexao.php"); // Conexão com o banco de dados

// Variáveis para armazenar erros e mensagens de sucesso
$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $id_filme = $_POST["id_filme"];
    $nome = $_POST["nome"];
    $ano = $_POST["ano"];
    $genero = $_POST["genero"];

    // Verificação de erros
    if (empty($nome)) {
        $erro = "O nome do filme é obrigatório.";
    }

    if (!preg_match('/^\d{4}$/', $ano)) {
        $erro = "O ano deve ter exatamente 4 dígitos.";
    }

    if (empty($genero)) {
        $erro = "O gênero é obrigatório.";
    }

    // Se não houver erro, atualiza o filme no banco
    if (empty($erro)) {
        $sql = "UPDATE filmes SET nome = ?, ano = ?, genero = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $ano, $genero, $id_filme);

        if ($stmt->execute()) {
            $sucesso = "Filme alterado com sucesso!";
        } else {
            $erro = "Erro ao alterar o filme: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<html>
<head>
    <title>Alterar Filme</title>
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
        <p><a href="cadastroFilmes.php"><font color="black">Cadastrar Filmes</font></a></p>
    </div>

    <div style="background-color: #ddd; min-height: 400px; width: 600px; float:left">
        <h2>Alterar Filme</h2>
        
        <?php if ($erro != "") { ?>
            <!-- Exibe o erro -->
            <div style="color: red;"><?= $erro; ?></div>
        <?php } ?>

        <?php if ($sucesso != "") { ?>
            <!-- Exibe a mensagem de sucesso -->
            <div style="color: green;"><?= $sucesso; ?></div>
        <?php } ?>

        <!-- Formulário para editar filme -->
        <form method="post" action="alterarFilme.php">
            NOME: <input type="text" name="nome" value="<?= isset($nome) ? $nome : ''; ?>"><br>
            ANO: <input type="text" name="ano" value="<?= isset($ano) ? $ano : ''; ?>"><br>
            GÊNERO: 
            <select name="genero">
                <option value="">Selecione um gênero</option>
                <?php 
                    // Consulta os gêneros ativos para preencher o select
                    $sql = "SELECT * FROM generos WHERE status = 1";
                    $resultado = $conn->query($sql);
                    if(!$resultado){
                        die("Erro ao buscar gêneros.");
                    }
                    while($row = $resultado->fetch_assoc()){
                ?>
                    <option value="<?=$row['generos'];?>" <?= (isset($genero) && $genero == $row['generos']) ? 'selected' : ''; ?>>
                        <?=$row['descricao'];?>
                    </option>
                <?php
                    }
                ?>
            </select><br>
            <input type="hidden" name="id_filme" value="<?= isset($id_filme) ? $id_filme : ''; ?>">
            <input type="submit" value="Alterar">
        </form>

    </div>
</div>

</body>
</html>
