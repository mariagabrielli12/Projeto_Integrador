<?php
$servidor = "localhost"; 
$usuario = "root"; 
$senha = ""; 
$banco = "sistema_web_creche"; // Corrigido para o nome do seu schema

// Cria a conexão
$conexao = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Define o charset
$conexao->set_charset("utf8");
?>