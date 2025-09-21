<?php
session_start();
define('PROJECT_ROOT', dirname(__DIR__));
require_once PROJECT_ROOT . '/conexao.php';

// Pega o ID do berçarista logado
$id_bercarista_logado = $_SESSION['id_usuario'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aluno_id = (int)($_POST['aluno_id'] ?? 0);
    $registros = $_POST['registros'] ?? [];
    $data_hoje = date('Y-m-d');

    if ($aluno_id === 0 || empty($registros)) {
        $_SESSION['mensagem_erro'] = "Erro: É necessário selecionar uma turma, uma criança e preencher pelo menos um campo da rotina.";
        header('Location: registro_diario.php');
        exit;
    }

    $conexao->begin_transaction();
    try {
        // Prepara a inserção na tabela `registros_diarios`
        // Usamos a coluna 'id_professor' para armazenar o ID de quem registou (seja professor ou berçarista)
        $sql = "INSERT INTO registros_diarios (id_aluno, id_professor, data, tipo_registro, descricao) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);

        foreach ($registros as $tipo => $detalhes) {
            // Só insere no banco se o campo tiver sido preenchido
            if (!empty(trim($detalhes))) {
                $stmt->bind_param("iisss", $aluno_id, $id_bercarista_logado, $data_hoje, $tipo, $detalhes);
                $stmt->execute();
            }
        }
        
        $conexao->commit();
        $_SESSION['mensagem_sucesso'] = "Rotina diária da criança salva com sucesso!";

    } catch (Exception $e) {
        $conexao->rollback();
        $_SESSION['mensagem_erro'] = "Ocorreu um erro ao salvar os registos: " . $e->getMessage();
    }
    
    $stmt->close();
    $conexao->close();
    header('Location: registro_diario.php');
    exit;
} else {
    // Redireciona se o acesso não for via POST
    header('Location: index.php');
    exit;
}
?>