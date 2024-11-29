<?php
// validacaoFormulario.php

// Função para validar se o formulário está vazio
function validarCamposObrigatorios($campos) {
    foreach ($campos as $campo => $nomeCampo) {
        if (empty($campo)) {
            return "O campo '$nomeCampo' é obrigatório.";
        }
    }
    return ""; // Retorna vazio se todos os campos forem preenchidos
}
?>