<?php
require_once '../conexao.php';
header('Content-Type: application/json');

$aluno_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$response = [];

if ($aluno_id > 0) {
    $sql = "
        SELECT 
            a.nome_completo, 
            DATE_FORMAT(a.data_nascimento, '%d/%m/%Y') as data_nascimento, 
            t.nome_turma,
            u.nome_completo as responsavel_nome,
            u.telefone as responsavel_contato
        FROM alunos a
        LEFT JOIN turmas t ON a.id_turma = t.id_turma
        LEFT JOIN alunos_responsaveis ar ON a.id_aluno = ar.id_aluno
        LEFT JOIN usuarios u ON ar.id_responsavel = u.id_usuario
        WHERE a.id_aluno = ?
        LIMIT 1
    ";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $aluno_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response = $result->fetch_assoc();
    } else {
        $response['error'] = "Aluno não encontrado.";
    }
    $stmt->close();
} else {
    $response['error'] = "ID do aluno inválido.";
}

echo json_encode($response);
$conexao->close();
?>