<?php
session_start();
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/conexao.php');

// Pega o ID do professor da sessão
$id_professor_logado = $_SESSION['id_usuario'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Recebe e valida os dados
    $turma_id = (int)($_POST['turma_id'] ?? 0);
    $data = $_POST['data'] ?? '';
    $titulo = trim($_POST['titulo'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    if ($turma_id === 0 || empty($data) || empty($titulo) || empty($observacoes)) {
        $_SESSION['mensagem_erro'] = "Erro: Todos os campos são obrigatórios.";
        header("Location: diario_bordo.php");
        exit();
    }

    // 2. Prepara e executa a inserção no banco
    $sql = "INSERT INTO diario_bordo (id_turma, id_professor, data_registro, titulo, observacoes) VALUES (?, ?, ?, ?, ?)";
    
    if($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("iisss", $turma_id, $id_professor_logado, $data, $titulo, $observacoes);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Registo salvo no diário de bordo com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao salvar o registo: " . $stmt->error;
        }
        $stmt->close();
    } else {
         $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta: " . $conexao->error;
    }

    $conexao->close();
    header("Location: diario_bordo.php");
    exit();
} else {
    header("Location: tela_principal_professor.php");
    exit();
}
?>