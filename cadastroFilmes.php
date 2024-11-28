<?php 
include("valida.php"); // Verifica se o usuário está autenticado
include("conexao.php"); // Conexão com o banco de dados

// Variável para armazenar erros
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta dados do formulário
    $descricao = $_POST["descricao"];
    $ano = $_POST["ano"];
    $genero = isset($_POST["genero"]) ? $_POST["genero"] : ""; // Garantir que o gênero seja definido
    $titulo = $_POST["titulo"]; // Considerando que o título é uma coluna adicional

    // Verificação de erros
    if (empty($descricao)) {
        $erro .= "A descrição do filme é obrigatória.<br>";
    }

    if (!preg_match('/^\d{4}$/', $ano)) {
        $erro .= "O ano deve ter exatamente 4 dígitos.<br>";
    }

    if (empty($genero)) {
        $erro .= "O gênero é obrigatório.<br>";
    }

    if (empty($titulo)) {
        $erro .= "O título do filme é obrigatório.<br>";
    }

    // Se não houver erro, insere o filme no banco
    if (empty($erro)) {
        // Preparação da consulta
        $sql = "INSERT INTO filmes (descricao, ano, genero, titulo) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Verifica se houve erro ao preparar a consulta
        if (!$stmt) {
            $erro = "Erro ao preparar a consulta: " . $conn->error;
        } else {
            // Bind dos parâmetros e execução
            $stmt->bind_param("ssis", $descricao, $ano, $genero, $titulo);

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
            <?php if (isset($_SESSION['nome'])) { ?>
                <span style="padding-left: 10px;">Olá <?= $_SESSION['nome']; ?></span>
            <?php } else { ?>
                <span>Usuário não autenticado.</span>
            <?php } ?>
        </div>

        <div style="width: 50%; float:left; text-align:right;">
            <span style="background-color:blue; margin-right:10px;"> 
                <a href="sair.php"><font color="black">SAIR</font></a>
            </span>
        </div>
    </div>
    
    <div id="menu" style="width: 200px; background-color: #f4f4f4; min-height: 400px; float: left;">
        <h2>Menu</h2>
        <p><a href="cadastroUsuarios.php"><font color="black">Cadastrar Usuários</font></a></p>
        <p><a href="cadastroFilmes.php"><font color="black">Cadastrar Filmes</font></a></p>
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
            Descrição: <input type="text" name="descricao" id="descricao" value="<?= isset($descricao) ? $descricao : ''; ?>"><br>
            Ano: <input type="text" name="ano" id="ano" value="<?= isset($ano) ? $ano : ''; ?>"><br>
            Título: <input type="text" name="titulo" id="titulo" value="<?= isset($titulo) ? $titulo : ''; ?>"><br>
            Gênero: 
            <select name="genero">
                <option value="">Selecione um gênero</option>
                <?php 
                    // Consulta os gêneros ativos
                    $sql = "SELECT * FROM generos WHERE status = 1";
                    $resultado = $conn->query($sql);
                    if(!$resultado){
                        error_log("Erro ao buscar gêneros: " . $conn->error);
                        echo "Erro ao buscar gêneros. Tente novamente mais tarde.";
                        exit;
                    }
                    while($row = $resultado->fetch_assoc()){
                ?>
                    <option value="<?=$row['genero'];?>" <?=(isset($genero) && $genero == $row['genero']) ? 'selected' : '';?>>
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
            $sql = "SELECT f.filme, f.descricao, f.ano, f.titulo, g.descricao AS genero
                    FROM filmes f
                    LEFT JOIN generos g ON f.genero = g.genero";  // LEFT JOIN para pegar o nome do gênero
            $resultado = $conn->query($sql);

            // Verifique se a consulta retornou algum erro
            if(!$resultado){
                error_log("Erro ao consultar filmes: " . $conn->error);
                echo "Erro ao consultar filmes. Tente novamente mais tarde.";
                exit;
            }
        ?>
        <table border="1" style="width: 100%; margin-top: 20px;">
            <tr>
                <th>Descrição</th>
                <th>Ano</th>
                <th>Título</th>
                <th>Gênero</th>
                <th>Alterar</th>
                <th>Apagar</th>
            </tr>
        
        <?php
        while($row = $resultado->fetch_assoc()){
        ?>
            <tr>
                <form method="post" action="alterarFilme.php"> 
                    <td><input type="text" name="descricao" value="<?=$row['descricao'];?>"></td>
                    <td><input type="text" name="ano" value="<?=$row['ano'];?>"></td>
                    <td><input type="text" name="titulo" value="<?=$row['titulo'];?>"></td>
                    <td>
                        <select name="genero">
                            <option value="">Selecione um gênero</option>
                            <?php 
                                // Consulta os gêneros ativos para preencher o select na alteração
                                $sql_genero = "SELECT * FROM generos WHERE status = 1";
                                $resultado_genero = $conn->query($sql_genero);
                                while($g = $resultado_genero->fetch_assoc()){
                            ?>
                                <option value="<?=$g['genero'];?>" <?= ($g['genero'] == $row['genero']) ? 'selected' : ''; ?>>
                                    <?=$g['descricao'];?>
                                </option>
                            <?php
                                }
                            ?>
                        </select>
                    </td>
                    <input type="hidden" name="filme" value="<?=$row['filme'];?>">
                    <td><input type="submit" value="Alterar"></td>
                </form>
                <form method="post" action="apagarFilme.php">
                    <input type="hidden" name="filme" value="<?=$row['filme'];?>">
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
