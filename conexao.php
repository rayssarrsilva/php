<?php

    $servidor = "localhost";
    $usuario = "root";
    $senha = "";
    $dbname = "cadastro_filmes";

    $conn = new mysqli($servidor,$usuario,$senha,$dbname);
    if($conn->connect_error){
        die("Falha na conexao ".$conn->connect_error);
    }
    ?>
