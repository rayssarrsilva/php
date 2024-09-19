<?php 
include("valida.php");
?>
<html>
<head>
    <title>Prncipal</title>
</head>
<body>

<div style="width: 800px; margin: 0 auto;">
    <div style="min-height: 100px; width: 100%; background-color: #4CAF50;";>
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
        <p>Item 2</p>
        <p>Item 3</p>
    </div>

    <div style="   background-color: #ddd; min-height: 400px; width: 600px; float:left">
        <h2>Conteúdo</h2>
        <p>Aqui vai o conteúdo principal.</p>
    </div>
</div>

</body>
</html>
