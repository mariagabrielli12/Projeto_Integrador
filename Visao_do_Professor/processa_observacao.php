<?php
session_start();
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/conexao.php');

// Pega o ID do professor da sessão
$id_professor_logado = $_SESSION['id'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recebe e valida os dados do formulário
    $aluno_id = (int)($_POST['aluno_id'] ?? 0);
    $data = $_POST['data'] ?? '';
    $area = trim($_POST['area'] ?? '');
    $habilidade = trim($_POST['habilidade'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    // Validação
    if ($aluno_id === 0 || empty($data) || empty($area) || empty($habilidade) || empty($descricao)) {
        $_SESSION['mensagem_erro'] = "Erro: Todos os campos são obrigatórios!";
        header("Location: registrar_observacao.php");
        exit();
    }

    // 2. Prepara e executa a inserção no banco de dados
    $sql = "INSERT INTO desenvolvimento_observacoes (data_observacao, area_desenvolvimento, habilidade_observada, descricao, aluno_id, professor_id) VALUES (?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("ssssii", $data, $area, $habilidade, $descricao, $aluno_id, $id_professor_logado);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Observação registrada com sucesso!";
            header("Location: desenvolvimento_aluno.php"); // Volta para a lista
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao salvar a observação: " . $stmt->error;
            header("Location: registrar_observacao.php"); // Volta para o formulário
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta: " . $conexao->error;
        header("Location: registrar_observacao.php");
    }
    
    $conexao->close();
    exit();

} else {
    header("Location: tela_principal_professor.php");
    exit();
}
?>