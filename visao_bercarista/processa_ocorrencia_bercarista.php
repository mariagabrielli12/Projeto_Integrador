<?php
session_start();
define('PROJECT_ROOT', dirname(__DIR__));
require_once PROJECT_ROOT . '/conexao.php';

// Pega o ID do berçarista da sessão
$id_bercarista_logado = $_SESSION['id_usuario'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $aluno_id = (int)($_POST['aluno_id'] ?? 0);
    $data_ocorrencia = $_POST['data_ocorrencia'] ?? '';
    $tipo = trim($_POST['tipo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($aluno_id === 0 || empty($data_ocorrencia) || empty($tipo) || empty($descricao)) {
        $_SESSION['mensagem_erro'] = "Erro: Todos os campos são obrigatórios!";
        header("Location: ocorrencias_bercarista.php");
        exit();
    }

    $sql = "INSERT INTO ocorrencias (id_aluno, id_registrado_por, data_ocorrencia, tipo, descricao) VALUES (?, ?, ?, ?, ?)";
    
    if($stmt = $conexao->prepare($sql)) {
        // id_registrado_por é o ID do berçarista logado
        $stmt->bind_param("iisss", $aluno_id, $id_bercarista_logado, $data_ocorrencia, $tipo, $descricao);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Ocorrência registrada com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao registrar a ocorrência: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta: " . $conexao->error;
    }
    
    $conexao->close();
    header("Location: ocorrencias_bercarista.php");
    exit();
}
?>