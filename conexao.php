<?php
$servidor = "localhost"; // Geralmente é localhost
$usuario = "root"; // Usuário padrão do XAMPP
$senha = ""; // Senha padrão do XAMPP é vazia
$banco = "sistema_creche"; // O nome do seu banco de dados

// Cria a conexão
$conexao = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Opcional: Define o charset para evitar problemas com acentuação
$conexao->set_charset("utf8");
?>