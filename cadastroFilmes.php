<?php 
include("valida.php");
include("conexao.php");

// Variável para armazenar erros
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST["cpf"];
    $nome = $_POST["nome"];
    $senha = $_POST["senha"];
    $genero = isset($_POST["genero"]) ? $_POST["genero"] : ""; // Garante que o gênero seja definido

    // Verificação do CPF
    if (!preg_match('/^\d{11}$/', $cpf)) {
        $erro = "O CPF deve conter exatamente 11 dígitos.";
    }

    // Verificação da senha usando regex
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $senha)) {
        $erro = "A senha deve ter pelo menos 6 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.";
    }

    // Se não houver erro, insere o usuário no banco
    if (empty($erro)) {
        // Inserir usuário no banco de dados, incluindo o código do gênero (que foi selecionado)
        $sql = "INSERT INTO usuarios (cpf, nome, senha, genero) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $cpf, $nome, $senha, $genero);

        if ($stmt->execute()) {
            echo "Usuário inserido com sucesso!";
        } else {
            $erro = "Erro ao inserir o usuário.";
        }

        $stmt->close();
    }
}
?>

<html>
<head>
    <title>Cadastro de filmes</title>
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
        <p>Item 3</p>
    </div>

    <div style="background-color: #ddd; min-height: 400px; width: 600px; float:left">
        <h2>Manutenção de usuários</h2>
        <h3>Criar novo usuário</h3>
        
        <?php if ($erro != "") { ?>
            <!-- Exibe o erro antes do formulário -->
            <div style="color: red;"><?= $erro; ?></div>
        <?php } ?>

        <form method="post" action="inserirUsuario.php">
            NOME: <input type="text" name="nome" id="nome" value="<?= isset($nome) ? $nome : ''; ?>"><br>
            ANO: <input type="text" name="ano" id="ano" value="<?= isset($ano) ? $ano : ''; ?>"><br>
            GENERO: 
            <select name="genero">
                <option value="">Selecione um gênero</option>
                <?php 
                    // Consulta os gêneros ativos
                    $sql = "SELECT * FROM generos WHERE status = 1"; // Filtra apenas os gêneros ativos
                    if(!$resultado = $conn->query($sql)){
                        die("Erro ao buscar gêneros.");
                    }
                    while($row = $resultado->fetch_assoc()){
                ?>
                    <option value="<?=$row['generos'];?>" <?= (isset($genero) && $genero == $row['generos']) ? 'selected' : ''; ?>>
                        <?=$row['descricao'];?> <!-- Exibe a descrição do gênero -->
                    </option>
                <?php
                    }
                ?>
            </select><br>
            <input type="submit" value="Inserir">
        </form>
        
        <br><br><hr><br><br>
        <?php
            // Consulta para exibir os usuários
            include("conexao.php");

            $sql = "SELECT nome, cpf, senha FROM usuarios";
            if(!$resultado = $conn->query($sql)){
                die("Erro ao consultar usuários.");
            }
        ?>
        <table>
            <tr>
                <td>Nome</td>
                <td>CPF</td>
                <td>Senha</td>
                <td>Alterar</td>
                <td>Apagar</td>
            </tr>
        
        <?php
        while($row = $resultado->fetch_assoc()){
        ?>
            <tr>
                <form method="post" action="alterarUsuario.php"> 
                    <input type="hidden" name="cpfAnterior" value="<?=$row['cpf'];?>">
                    <td>
                        <input type="text" name="nome" value="<?=$row['nome'];?>">
                    </td>
                    <td><input type="text" name="cpf" value="<?=$row['cpf'];?>"></td>
                    <td><input type="text" name="senha" value="<?=$row['senha'];?>"></td>
                    <td><input type="submit" value="Alterar"></td>
                </form>
                <form method="post" action="apagarUsuario.php">
                    <input type="hidden" name="cpf" value="<?=$row['cpf'];?>">
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


