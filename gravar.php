<?php
session_start();
print_r($_SESSION);
echo "<br>";
echo "conteúdo: ".$_SESSION["cpf"]."-".$_SESSION["senha"];
?>