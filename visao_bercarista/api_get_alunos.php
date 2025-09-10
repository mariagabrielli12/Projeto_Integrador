<?php
require_once '../conexao.php';
header('Content-Type: application/json');

$turma_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;

if ($turma_id > 0) {
    $stmt = $conexao->prepare("SELECT id_aluno, nome_completo FROM alunos WHERE id_turma = ? ORDER BY nome_completo");
    $stmt->bind_param("i", $turma_id);
    $stmt->execute();
    $alunos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode($alunos);
} else {
    echo json_encode([]);
}

$conexao->close();
?>