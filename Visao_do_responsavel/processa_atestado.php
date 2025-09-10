<?php
session_start();
define('PROJECT_ROOT', dirname(__DIR__));
require_once PROJECT_ROOT . '/conexao.php';

// Pega o ID do responsável da sessão
$id_responsavel_logado = $_SESSION['id_usuario'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Busca o ID do aluno associado a este responsável
    $id_aluno_associado = null;
    $stmt_aluno = $conexao->prepare("SELECT id_aluno FROM alunos_responsaveis WHERE id_responsavel = ? LIMIT 1");
    $stmt_aluno->bind_param("i", $id_responsavel_logado);
    $stmt_aluno->execute();
    $result_aluno = $stmt_aluno->get_result();
    if ($result_aluno->num_rows > 0) {
        $id_aluno_associado = $result_aluno->fetch_assoc()['id_aluno'];
    }
    $stmt_aluno->close();

    if (!$id_aluno_associado) {
        $_SESSION['mensagem_erro'] = "Erro: Nenhuma criança associada a este responsável.";
        header('Location: atestados_responsavel.php');
        exit;
    }

    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $motivo = trim($_POST['motivo']) ?? '';

    // Validação
    if (empty($data_inicio) || empty($data_fim) || !isset($_FILES['arquivo_atestado']) || $_FILES['arquivo_atestado']['error'] != 0) {
        $_SESSION['mensagem_erro'] = "Erro: Por favor, preencha as datas e selecione um ficheiro válido.";
        header('Location: atestados_responsavel.php');
        exit;
    }

    // Lógica de Upload
    $upload_dir = PROJECT_ROOT . '/uploads/atestados/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $nome_ficheiro = uniqid() . '-' . basename($_FILES['arquivo_atestado']['name']);
    $caminho_final = $upload_dir . $nome_ficheiro;
    $caminho_bd = 'uploads/atestados/' . $nome_ficheiro;

    if (move_uploaded_file($_FILES['arquivo_atestado']['tmp_name'], $caminho_final)) {
        $sql = "INSERT INTO atestados (id_aluno, data_inicio, data_fim, motivo, caminho_anexo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("issss", $id_aluno_associado, $data_inicio, $data_fim, $motivo, $caminho_bd);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Atestado enviado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao salvar o registo no banco de dados.";
            unlink($caminho_final); // Apaga o ficheiro se o DB falhar
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao fazer o upload do ficheiro.";
    }

    $conexao->close();
    header('Location: atestados_responsavel.php');
    exit;
}
?>