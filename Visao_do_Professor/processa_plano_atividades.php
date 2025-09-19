<?php
session_start();
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/conexao.php');

// Pega o ID do professor da sessão
$id_professor_logado = $_SESSION['id'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recebe e valida os dados do formulário
    $titulo = trim($_POST['titulo'] ?? '');
    $turma_id = (int)($_POST['turma_id'] ?? 0);
    $data = $_POST['data'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');

    // Validação simples
    if (empty($titulo) || $turma_id === 0 || empty($data) || empty($descricao)) {
        $_SESSION['mensagem_erro'] = "Erro: Todos os campos são obrigatórios!";
        header("Location: plano_atividades.php");
        exit();
    }

    // 2. Prepara e executa a inserção no banco de dados de forma segura
    $sql = "INSERT INTO atividade (titulo, data, decricao, usuario_id, Turmas_ID_Turma) VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("sssii", $titulo, $data, $descricao, $id_professor_logado, $turma_id);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Atividade planejada com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao salvar a atividade: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta: " . $conexao->error;
    }
    
    $conexao->close();
    // Redireciona de volta para a página do plano
    header("Location: plano_atividades.php");
    exit();

} else {
    // Se o acesso não for via POST, redireciona para a página inicial
    header("Location: tela_principal_professor.php");
    exit();
}
?>