<?php
session_start();
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/conexao.php');

// Pega o ID do professor da sessão
$id_professor_logado = $_SESSION['id_usuario'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Receber e validar os dados
    $aluno_id = (int)($_POST['aluno_id'] ?? 0);
    $data_ocorrencia = $_POST['data_ocorrencia'] ?? '';
    $tipo = trim($_POST['tipo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    // Validação
    if ($aluno_id === 0 || empty($data_ocorrencia) || empty($tipo) || empty($descricao)) {
        $_SESSION['mensagem_erro'] = "Erro: Todos os campos são obrigatórios!";
        header("Location: ocorrencias_professor.php");
        exit();
    }

    // 2. Preparar e executar a inserção no banco de dados
    $sql = "INSERT INTO ocorrencias (id_aluno, id_registrado_por, data_ocorrencia, tipo, descricao) VALUES (?, ?, ?, ?, ?)";
    
    if($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("iisss", $aluno_id, $id_professor_logado, $data_ocorrencia, $tipo, $descricao);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Ocorrência registada com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao registar a ocorrência: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta: " . $conexao->error;
    }
    
    $conexao->close();
    header("Location: ocorrencias_professor.php");
    exit();

} else {
    $_SESSION['mensagem_erro'] = "Acesso inválido!";
    header("Location: tela_principal_professor.php");
    exit();
}
?>