<?php
session_start();
require_once '../conexao.php';

// Simulação de dados do usuário logado
$responsavel_logado_id = 3; 
$aluno_id_associado = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $motivo = trim($_POST['motivo']) ?? ''; // Usar trim para limpar espaços

    // Validação básica dos dados
    if (empty($data_inicio) || empty($data_fim) || !isset($_FILES['arquivo_atestado']) || $_FILES['arquivo_atestado']['error'] != 0) {
        $_SESSION['mensagem_erro'] = "Erro: Por favor, preencha as datas e selecione um arquivo válido.";
        header('Location: atestados_responsavel.php');
        exit;
    }

    // --- LÓGICA DE UPLOAD MAIS SEGURA ---
    $upload_dir = __DIR__ . '/../uploads/atestados/';
    
    // Gera um nome de arquivo único para segurança
    $nome_original = basename($_FILES['arquivo_atestado']['name']);
    $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
    $nome_seguro = uniqid('atestado_', true) . '.' . $extensao;
    
    $caminho_arquivo_final = $upload_dir . $nome_seguro;
    $caminho_bd = 'uploads/atestados/' . $nome_seguro;

    // --- INSERÇÃO NO BANCO DE DADOS (COM STATUS) ---
    if (move_uploaded_file($_FILES['arquivo_atestado']['tmp_name'], $caminho_arquivo_final)) {
        // Query SQL ATUALIZADA para incluir o status
        $sql = "INSERT INTO atestados (aluno_id, responsavel_id, data_inicio_afastamento, data_fim_afastamento, motivo, caminho_arquivo, status) VALUES (?, ?, ?, ?, ?, ?, 'Pendente')";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iissss", $aluno_id_associado, $responsavel_logado_id, $data_inicio, $data_fim, $motivo, $caminho_bd);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Atestado enviado com sucesso! Aguardando aprovação da secretaria.";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao salvar o registro no banco de dados: " . $stmt->error;
            unlink($caminho_arquivo_final); // Deleta o arquivo se o DB falhar
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao fazer o upload do arquivo.";
    }

    $conexao->close();
    header('Location: atestados_responsavel.php');
    exit;
}
?>