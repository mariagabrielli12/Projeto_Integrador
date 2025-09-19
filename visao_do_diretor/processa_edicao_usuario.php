<?php
session_start();
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
require_once PROJECT_ROOT . '/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = (int)($_POST['id_usuario'] ?? 0);
    if ($id_usuario === 0) {
        $_SESSION['mensagem_erro'] = "ID do utilizador inválido.";
        header("Location: index.php");
        exit();
    }

    // Coleta os dados
    $nome_completo = trim($_POST['nome_completo']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $matricula = trim($_POST['matricula']);
    $ativo = (int)($_POST['ativo']);
    $senha = $_POST['senha'];

    // Lógica para atualizar a senha apenas se uma nova for fornecida
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nome_completo=?, cpf=?, email=?, telefone=?, matricula=?, ativo=?, senha_hash=? WHERE id_usuario=?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sssssisi", $nome_completo, $cpf, $email, $telefone, $matricula, $ativo, $senha_hash, $id_usuario);
    } else {
        // Se a senha estiver em branco, não atualiza a coluna senha_hash
        $sql = "UPDATE usuarios SET nome_completo=?, cpf=?, email=?, telefone=?, matricula=?, ativo=? WHERE id_usuario=?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sssssii", $nome_completo, $cpf, $email, $telefone, $matricula, $ativo, $id_usuario);
    }

    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = "Utilizador atualizado com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao atualizar o utilizador: " . $stmt->error;
    }
    $stmt->close();
    $conexao->close();

    // Redireciona de volta para a última página visitada (a lista)
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>