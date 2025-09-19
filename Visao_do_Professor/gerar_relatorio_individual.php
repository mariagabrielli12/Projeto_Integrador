<?php
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aluno_id = $_POST['aluno_id'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    // Buscar dados do aluno
    $aluno_info = $conexao->query("SELECT nome_completo FROM alunos WHERE id_aluno = $aluno_id")->fetch_assoc();

    // Buscar observações de desenvolvimento
    $observacoes = $conexao->query("
        SELECT * FROM desenvolvimento_observacoes
        WHERE id_aluno = $aluno_id AND data_observacao BETWEEN '$data_inicio' AND '$data_fim'
    ")->fetch_all(MYSQLI_ASSOC);

    // Renderizar o relatório (exemplo simples)
    echo "<h1>Relatório de Desenvolvimento</h1>";
    echo "<h2>" . $aluno_info['nome_completo'] . "</h2>";
    echo "<h3>Período: " . date('d/m/Y', strtotime($data_inicio)) . " a " . date('d/m/Y', strtotime($data_fim)) . "</h3>";
    foreach($observacoes as $obs) {
        echo "<p><strong>" . $obs['area_desenvolvimento'] . ":</strong> " . $obs['descricao'] . "</p>";
    }
}
?>