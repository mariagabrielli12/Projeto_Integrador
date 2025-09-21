<?php
session_start();
define('PROJECT_ROOT', dirname(__DIR__));
require_once PROJECT_ROOT . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $turma_id = (int)($_POST['turma_id'] ?? 0);
    $data = $_POST['data'] ?? '';
    $presencas = $_POST['status'] ?? [];

    if ($turma_id === 0 || empty($data) || empty($presencas)) {
        $_SESSION['mensagem_erro'] = "Erro: Turma, data e pelo menos um aluno são necessários.";
        header('Location: presenca_bercarista.php');
        exit;
    }

    $conexao->begin_transaction();
    try {
        // Usamos REPLACE INTO que deleta a entrada antiga (se houver) e insere a nova.
        // Isso evita erros de chave duplicada e garante que a presença do dia seja sempre a última enviada.
        $sql = "REPLACE INTO registro_presenca (id_aluno, id_turma, data, presenca) VALUES (?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);

        foreach ($presencas as $aluno_id => $status) {
            $stmt->bind_param("iiss", $aluno_id, $turma_id, $data, $status);
            $stmt->execute();
        }
        
        $stmt->close();
        $conexao->commit();
        $_SESSION['mensagem_sucesso'] = "Presença registrada com sucesso para o dia " . date('d/m/Y', strtotime($data)) . "!";

    } catch (Exception $e) {
        $conexao->rollback();
        $_SESSION['mensagem_erro'] = "Ocorreu um erro ao salvar a presença: " . $e->getMessage();
    }
    
    header('Location: presenca_bercarista.php');
    exit;
}
?>