<?php
session_start();
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/conexao.php');

$id_professor_logado = $_SESSION['id_usuario'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $titulo = trim($_POST['titulo'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $duracao = trim($_POST['duracao'] ?? '');
    $objetivos = trim($_POST['objetivos'] ?? '');
    $materiais = trim($_POST['materiais'] ?? '');

    if (empty($titulo)) {
        $_SESSION['mensagem_erro'] = "Erro: O título da atividade é obrigatório.";
        header("Location: atividades_ludicas.php");
        exit();
    }

    $sql = "INSERT INTO atividades_ludicas (titulo, categoria, duracao_sugerida, objetivos, materiais, id_professor_criador) VALUES (?, ?, ?, ?, ?, ?)";
    
    if($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("sssssi", $titulo, $categoria, $duracao, $objetivos, $materiais, $id_professor_logado);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Atividade lúdica adicionada à biblioteca com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao salvar a atividade: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta: " . $conexao->error;
    }

    header("Location: atividades_ludicas.php");
    exit();
}
?>