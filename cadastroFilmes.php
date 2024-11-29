<?php 
include("valida.php"); // Verifica se o usuário está autenticado
include("conexao.php"); // Conexão com o banco de dados
include("validacaoVazio.php");

// Variável para armazenar erros
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta dados do formulário
    $nome = $_POST["nome"];
    $ano = $_POST["ano"];
    $genero = isset($_POST["genero"]) ? $_POST["genero"] : ""; // Garantir que o gênero seja definido

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

    // Se não houver erro, insere o filme no banco
    if (empty($erro)) {
        $sql = "INSERT INTO filmes (nome, ano, genero) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $ano, $genero);

        if ($stmt->execute()) {
            // Redireciona para evitar reenvio de formulário e mostrar sucesso
            header("Location: cadastroFilmes.php?success=1");
            exit; // Para garantir que o script pare aqui e evite qualquer saída adicional
        } else {
            $erro = "Erro ao inserir o filme: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<html>
<head>
    <title>Cadastro de Filmes</title>
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
        <p><a href="cadastroGeneros.php"><font color="black">Cadastrar Generos</font></a></p>
    </div>

    <div style="background-color: #ddd; min-height: 400px; width: 600px; float:left">
        <h2>Cadastro de Filme</h2>
        
        <?php if ($erro != "") { ?>
            <!-- Exibe o erro antes do formulário -->
            <div style="color: red;"><?= $erro; ?></div>
        <?php } ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1) { ?>
            <!-- Exibe uma mensagem de sucesso após o redirecionamento -->
            <div style="color: green;">Filme inserido com sucesso!</div>
        <?php } ?>

        <!-- Formulário para inserir filme -->
        <form method="post" action="cadastroFilmes.php">
            NOME: <input type="text" name="nome" id="nome" value="<?= isset($nome) ? $nome : ''; ?>"><br>
            ANO: <input type="text" name="ano" id="ano" value="<?= isset($ano) ? $ano : ''; ?>"><br>
            GENERO: 
            <select name="genero">
                <option value="">Selecione um gênero</option>
                <?php 
                    // Consulta os gêneros ativos
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
            <input type="submit" value="Inserir">
        </form>
        
        <br><br><hr><br><br>
        <?php
            // Consulta para exibir os filmes cadastrados com o nome do gênero
            $sql = "SELECT f.id, f.nome, f.ano, g.descricao AS genero
                    FROM filmes f
                    LEFT JOIN generos g ON f.genero = g.generos";  // LEFT JOIN para pegar o nome do gênero
            $resultado = $conn->query($sql);

            // Verifique se a consulta retornou algum erro
            if(!$resultado){
                die("Erro ao consultar filmes: " . $conn->error);  // Exibe o erro caso ocorra
            }
        ?>
        <table>
            <tr>
                <td>Nome</td>
                <td>Ano</td>
                <td>Gênero</td>
                <td>Alterar</td>
                <td>Apagar</td>
            </tr>
        
        <?php
        while($row = $resultado->fetch_assoc()){
        ?>
            <tr>
                <form method="post" action="alterarFilme.php"> 
                    <td><input type="text" name="nome" value="<?=$row['nome'];?>"></td>
                    <td><input type="text" name="ano" value="<?=$row['ano'];?>"></td>
                    <td>
                        <select name="genero">
                            <option value="">Selecione um gênero</option>
                            <?php 
                                // Consulta os gêneros ativos para preencher o select na alteração
                                $sql_generos = "SELECT * FROM generos WHERE status = 1";
                                $resultado_generos = $conn->query($sql_generos);
                                while($g = $resultado_generos->fetch_assoc()){
                            ?>
                                <option value="<?=$g['generos'];?>" <?= ($g['generos'] == $row['genero']) ? 'selected' : ''; ?>>
                                    <?=$g['descricao'];?>
                                </option>
                            <?php
                                }
                            ?>
                        </select>
                    </td>
                    <input type="hidden" name="id_filme" value="<?=$row['id'];?>">
                    <td><input type="submit" value="Alterar"></td>
                </form>
                <form method="post" action="apagarFilme.php">
                    <input type="hidden" name="id_filme" value="<?=$row['id'];?>">
                    <td><input type="submit" value="Apagar"></td>
                </form>
            </tr>
        <?php
        }
        ?>  
        </table>
    </div>
</div>

</body>
</html>
