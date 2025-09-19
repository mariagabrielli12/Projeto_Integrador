<?php
session_start();
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/conexao.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Coletar e validar dados
    $id_tipo = (int)($_POST['id_tipo'] ?? 0);
    $nome_completo = trim($_POST['nome_completo'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $matricula = trim($_POST['matricula'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome_completo) || empty($matricula) || empty($senha) || $id_tipo === 0) {
        $_SESSION['mensagem_erro'] = "Erro: Nome, Matrícula, Senha e Tipo de Utilizador são obrigatórios.";
        header("Location: index.php");
        exit();
    }

    // 2. Gerar o hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // 3. Inserir no banco de dados
    $sql = "INSERT INTO usuarios (nome_completo, cpf, email, telefone, matricula, senha_hash, id_tipo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    if($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("ssssssi", $nome_completo, $cpf, $email, $telefone, $matricula, $senha_hash, $id_tipo);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Utilizador cadastrado com sucesso!";
        } else {
            // Verifica se é um erro de duplicidade
            if ($conexao->errno == 1062) {
                 $_SESSION['mensagem_erro'] = "Erro: A Matrícula, CPF ou Email informado já existe no sistema.";
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao cadastrar o utilizador: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
         $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta: " . $conexao->error;
    }

    $conexao->close();
    header("Location: index.php");
    exit();

} else {
    // Redireciona se o acesso não for via POST
    header("Location: index.php");
    exit();
}
?>