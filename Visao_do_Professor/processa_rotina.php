<?php
session_start();
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/conexao.php');

$id_professor_logado = $_SESSION['id_usuario'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $turma_id = (int)($_POST['turma_id'] ?? 0);
    $dia_semana = $_POST['dia_semana'] ?? '';
    $horarios_inicio = $_POST['horario_inicio'] ?? [];
    $horarios_fim = $_POST['horario_fim'] ?? [];
    $atividades = $_POST['atividade_descricao'] ?? [];

    if ($turma_id === 0 || empty($dia_semana) || empty($atividades)) {
        $_SESSION['mensagem_erro'] = "Erro: Turma, dia da semana e pelo menos uma atividade são obrigatórios.";
        header("Location: rotinas_diarias.php");
        exit();
    }

    $conexao->begin_transaction();
    try {
        // Primeiro, apaga a rotina antiga para este dia e turma, para substituir pela nova.
        $stmt_delete = $conexao->prepare("DELETE FROM rotinas WHERE id_turma = ? AND dia_semana = ?");
        $stmt_delete->bind_param("is", $turma_id, $dia_semana);
        $stmt_delete->execute();
        $stmt_delete->close();

        // Agora, insere os novos itens da rotina
        $sql_insert = "INSERT INTO rotinas (id_turma, dia_semana, horario_inicio, horario_fim, descricao_atividade) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conexao->prepare($sql_insert);

        for ($i = 0; $i < count($atividades); $i++) {
            if (!empty($horarios_inicio[$i]) && !empty($horarios_fim[$i]) && !empty($atividades[$i])) {
                $stmt_insert->bind_param("issss", $turma_id, $dia_semana, $horarios_inicio[$i], $horarios_fim[$i], $atividades[$i]);
                $stmt_insert->execute();
            }
        }
        $stmt_insert->close();

        $conexao->commit();
        $_SESSION['mensagem_sucesso'] = "Rotina para $dia_semana salva com sucesso!";
    } catch (Exception $e) {
        $conexao->rollback();
        $_SESSION['mensagem_erro'] = "Erro ao salvar a rotina: " . $e->getMessage();
    }
    
    header("Location: rotinas_diarias.php");
    exit();
}
?>