<?php 
include("valida.php"); // Verifica se o usuário está autenticado
include("conexao.php"); // Conexão com o banco de dados

// Variável para armazenar erros
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta dados do formulário
    $descricao = $_POST["descricao"];
    $status = isset($_POST["status"]) ? $_POST["status"] : ""; // Garantir que o status seja definido

    // Verificação de erros
    if (empty($descricao)) {
        $erro = "A descrição do gênero é obrigatória.";
    }

    if (!in_array($status, [0, 1])) {
        $erro = "O status deve ser 0 (inativo) ou 1 (ativo).";
    }

    // Se não houver erro, insere o gênero no banco
    if (empty($erro)) {
        $sql = "INSERT INTO generos (descricao, status) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $descricao, $status);

        if ($stmt->execute()) {
            // Redireciona para evitar reenvio de formulário e mostrar sucesso
            header("Location: cadastroGeneros.php?success=1");
            exit; // Para garantir que o script pare aqui e evite qualquer saída adicional
        } else {
            $erro = "Erro ao inserir o gênero: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<html>
<head>
    <title>Cadastro de Gêneros</title>
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
        <h2>Cadastro de Gênero</h2>
        
        <?php if ($erro != "") { ?>
            <!-- Exibe o erro antes do formulário -->
            <div style="color: red;"><?= $erro; ?></div>
        <?php } ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1) { ?>
            <!-- Exibe uma mensagem de sucesso após o redirecionamento -->
            <div style="color: green;">Gênero inserido com sucesso!</div>
        <?php } ?>

        <!-- Formulário para inserir gênero -->
        <form method="post" action="cadastroGeneros.php">
            DESCRIÇÃO: <input type="text" name="descricao" id="descricao" value="<?= isset($descricao) ? $descricao : ''; ?>"><br>
            STATUS:
            <select name="status">
                <option value="1" <?= isset($status) && $status == 1 ? 'selected' : ''; ?>>Ativo</option>
                <option value="0" <?= isset($status) && $status == 0 ? 'selected' : ''; ?>>Inativo</option>
            </select><br>
            <input type="submit" value="Inserir">
        </form>
        
        <br><br><hr><br><br>
        <?php
            // Consulta para exibir os gêneros cadastrados
            $sql = "SELECT generos, descricao, status FROM generos";  // Consulta para pegar os dados dos gêneros
            $resultado = $conn->query($sql);

            // Verifique se a consulta retornou algum erro
            if(!$resultado){
                die("Erro ao consultar gêneros: " . $conn->error);  // Exibe o erro caso ocorra
            }
        ?>
        <table border="1">
            <tr>
                <td>Descrição</td>
                <td>Status</td>
                <td>Alterar</td>
                <td>Apagar</td>
            </tr>
        
        <?php
        while($row = $resultado->fetch_assoc()){
        ?>
            <tr>
                <form method="post" action="alterarGeneros.php"> 
                    <td><input type="text" name="descricao" value="<?=$row['descricao'];?>"></td>
                    <td>
                        <select name="status">
                            <option value="1" <?= ($row['status'] == 1) ? 'selected' : ''; ?>>Ativo</option>
                            <option value="0" <?= ($row['status'] == 0) ? 'selected' : ''; ?>>Inativo</option>
                        </select>
                    </td>
                    <input type="hidden" name="id_genero" value="<?=$row['generos'];?>">
                    <td><input type="submit" value="Alterar"></td>
                </form>
                <form method="post" action="apagarGeneros.php">
                    <input type="hidden" name="id_genero" value="<?=$row['generos'];?>">
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
