<?php
// Define a constante que aponta para a pasta raiz do projeto
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(dirname(__DIR__)));
}
// Inicia a sessão para uso futuro
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// ---- VERIFICAÇÃO DE SEGURANÇA ----
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["perfil"] !== 'Diretor') {
    header("location: ../../tela_login/index.php");
    exit;
}
// Inclui o ficheiro de conexão com o banco de dados
require_once PROJECT_ROOT . '/conexao.php';
// Pega os dados do utilizador da sessão
$id_diretor_logado = $_SESSION['id_usuario'];
$nome_diretor_logado = $_SESSION['nome_completo'];
// Função para verificar qual item do menu deve estar ativo
function is_active($page_name) {
    if (basename($_SERVER['PHP_SELF']) == $page_name) {
        return 'active';
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Painel do Diretor' : 'Painel do Diretor'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo"><i class="fas fa-graduation-cap"></i><h1>Rede Educacional</h1></div>
        <ul class="menu">
            <li class="menu-item <?php echo is_active('index.php'); ?>">
                <a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
            
            <li class="menu-section-title">Visão Geral</li>
            <li class="menu-item <?php echo is_active('visualizar_turmas.php'); ?>">
                <a href="visualizar_turmas.php"><i class="fas fa-chalkboard"></i><span>Todas as Turmas</span></a>
            </li>
            <li class="menu-item <?php echo is_active('visualizar_alunos.php'); ?>">
                <a href="visualizar_alunos.php"><i class="fas fa-users"></i><span>Todos os Alunos</span></a>
            </li>
            <li class="menu-item <?php echo is_active('visualizar_ocorrencias.php'); ?>">
                <a href="visualizar_ocorrencias.php"><i class="fas fa-exclamation-triangle"></i><span>Ocorrências</span></a>
            </li>
             <li class="menu-item <?php echo is_active('visualizar_diario_bordo.php'); ?>">
                <a href="visualizar_diario_bordo.php"><i class="fas fa-book-open"></i><span>Diário de Bordo</span></a>
            </li>

            <li class="menu-section-title">Gestão de Utilizadores</li>
            <li class="menu-item <?php echo is_active('cadastro_usuario.php'); ?>">
                <a href="cadastro_usuario.php"><i class="fas fa-user-plus"></i><span>Cadastrar Utilizador</span></a>
            </li>
            <li class="menu-item <?php echo is_active('listagem_secretario.php'); ?>">
                <a href="listagem_secretario.php"><i class="fas fa-user-tie"></i><span>Secretários</span></a>
            </li>
            <li class="menu-item <?php echo is_active('listagem_professor.php'); ?>">
                <a href="listagem_professor.php"><i class="fas fa-chalkboard-teacher"></i><span>Professores</span></a>
            </li>
            <li class="menu-item <?php echo is_active('listagem_bercarista.php'); ?>">
                <a href="listagem_bercarista.php"><i class="fas fa-baby"></i><span>Berçaristas</span></a>
            </li>
            <li class="menu-item <?php echo is_active('listagem_responsaveis.php'); ?>">
                <a href="listagem_responsaveis.php"><i class="fas fa-user-shield"></i><span>Responsáveis</span></a>
            </li>
        </ul>
        <div class="logout">
            <a href="/Projeto_Integrador-main/tela_login/logout.php" style="color:inherit; text-decoration:none;"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
        </div>
    </div>
    <div class="main-content">
        <div class="header">
            <div class="page-title">
                <i class="<?php echo isset($page_icon) ? $page_icon : 'fas fa-home'; ?>"></i>
                <h2><?php echo isset($page_title) ? $page_title : 'Início'; ?></h2>
            </div>
            <div class="user-info">
                <div class="user-avatar">AD</div>
                <span><?php echo htmlspecialchars($nome_diretor_logado); ?></span>
            </div>
        </div>
        <div class="content-container">
            <?php
            if (isset($_SESSION['mensagem_sucesso'])) {
                echo '<div class="alert success" style="margin: 15px; background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px;">' . htmlspecialchars($_SESSION['mensagem_sucesso']) . '</div>';
                unset($_SESSION['mensagem_sucesso']);
            }
            if (isset($_SESSION['mensagem_erro'])) {
                echo '<div class="alert error" style="margin: 15px; background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">' . htmlspecialchars($_SESSION['mensagem_erro']) . '</div>';
                unset($_SESSION['mensagem_erro']);
            }
            ?>