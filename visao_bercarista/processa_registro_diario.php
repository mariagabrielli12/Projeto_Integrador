<?php
session_start();
require_once '../conexao.php';

// Simulação de ID do berçarista logado
$bercarista_logado_id = 4;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aluno_id = $_POST['aluno_id'] ?? null;
    $registros = $_POST['registros'] ?? [];
    $data_hoje = date('Y-m-d');
    $hora_agora = date('H:i:s');

    if (empty($aluno_id) || empty($registros)) {
        $_SESSION['mensagem_erro'] = "Erro: É necessário selecionar uma criança e preencher pelo menos um campo.";
        header('Location: registro_diario_bercarista.php');
        exit;
    }

    $conexao->begin_transaction(); // Inicia uma transação para garantir que todos os registros sejam salvos juntos

    try {
        $sql = "INSERT INTO registros_diarios (aluno_id, data, tipo, detalhes, horario, registrado_por_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);

        // Loop através de cada campo de avaliação enviado
        foreach ($registros as $tipo => $detalhes) {
            // Só insere no banco se o campo tiver sido preenchido
            if (!empty(trim($detalhes))) {
                $stmt->bind_param("issssi", $aluno_id, $data_hoje, $tipo, $detalhes, $hora_agora, $bercarista_logado_id);
                $stmt->execute();
            }
        }
        
        $conexao->commit(); // Confirma as inserções no banco
        $_SESSION['mensagem_sucesso'] = "Avaliação diária salva com sucesso!";

    } catch (mysqli_sql_exception $exception) {
        $conexao->rollback(); // Desfaz as inserções em caso de erro
        $_SESSION['mensagem_erro'] = "Ocorreu um erro ao salvar os registros: " . $exception->getMessage();
    }
    
    $stmt->close();
    $conexao->close();
    header('Location: registro_diario_bercarista.php');
    exit;
}
?>