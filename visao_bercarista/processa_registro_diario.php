<?php
session_start();
define('PROJECT_ROOT', dirname(__DIR__));
require_once PROJECT_ROOT . '/conexao.php';

// Pega o ID do berçarista da sessão
$id_bercarista_logado = $_SESSION['id_usuario'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $turma_id = (int)($_POST['turma_id'] ?? 0);
    $aluno_id = (int)($_POST['aluno_id'] ?? 0);
    $registros = $_POST['registros'] ?? [];
    $data_hoje = date('Y-m-d');

    if ($aluno_id === 0 || empty($registros)) {
        $_SESSION['mensagem_erro'] = "Erro: É necessário selecionar uma turma, uma criança e preencher pelo menos um campo.";
        header('Location: registro_diario.php');
        exit;
    }

    $conexao->begin_transaction();
    try {
        // Usa a tabela `registros_diarios` que criámos
        $sql = "INSERT INTO registros_diarios (id_aluno, id_professor, data, tipo_registro, descricao) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);

        foreach ($registros as $tipo => $detalhes) {
            // Só insere no banco se o campo tiver sido preenchido
            if (!empty(trim($detalhes))) {
                // Nota: Usamos id_professor como a coluna que registou, mesmo sendo um berçarista
                $stmt->bind_param("iisss", $aluno_id, $id_bercarista_logado, $data_hoje, $tipo, $detalhes);
                $stmt->execute();
            }
        }
        
        $conexao->commit();
        $_SESSION['mensagem_sucesso'] = "Avaliação diária salva com sucesso!";

    } catch (Exception $e) {
        $conexao->rollback();
        $_SESSION['mensagem_erro'] = "Ocorreu um erro ao salvar os registos: " . $e->getMessage();
    }
    
    $stmt->close();
    $conexao->close();
    header('Location: registro_diario.php');
    exit;
}
?>