<?php
session_start();
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/conexao.php');

// Pega o ID do professor da sessão
$id_professor_logado = $_SESSION['id_usuario'] ?? 1; // Ajustado para id_usuario

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Recebe e valida os dados
    $destinatario_turma_id = $_POST['destinatario_turma_id'];
    $assunto = trim($_POST['assunto'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if (empty($destinatario_turma_id) || empty($assunto) || empty($mensagem)) {
        $_SESSION['mensagem_erro'] = "Erro: Todos os campos são obrigatórios.";
        header("Location: criar_comunicado.php");
        exit();
    }

    $turma_id_db = is_numeric($destinatario_turma_id) ? (int)$destinatario_turma_id : null;

    // 2. Prepara e executa a inserção no banco
    $sql = "INSERT INTO comunicados (remetente_id, destinatario_turma_id, assunto, mensagem) VALUES (?, ?, ?, ?)";
    
    if($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("isss", $id_professor_logado, $turma_id_db, $assunto, $mensagem);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Comunicado enviado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao enviar o comunicado: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta: " . $conexao->error;
    }

    $conexao->close();
    header("Location: comunicados_professor.php");
    exit();

} else {
    header("Location: tela_principal_professor.php");
    exit();
}
?>