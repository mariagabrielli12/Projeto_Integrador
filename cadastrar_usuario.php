<?php
// Inclui a conexão com o banco
require_once 'conexao.php';

// --- DADOS DO NOVO USUÁRIO ---
$nome = "Admin Diretor";
$matricula = "diretor01";
$senha_plana = "senha123"; // A senha que será digitada no login
$perfil = "diretor";

// --- LÓGICA DE CADASTRO ---
// Gera o hash seguro da senha
$senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);

// Prepara o SQL para inserir o usuário
$sql = "INSERT INTO usuarios (nome, matricula, senha_hash, perfil) VALUES (?, ?, ?, ?)";

if ($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param("ssss", $nome, $matricula, $senha_hash, $perfil);

    if ($stmt->execute()) {
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }
    $stmt->close();
}
$conexao->close();
?>