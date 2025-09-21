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
function is_active($page_names) {
    $pages = is_array($page_names) ? $page_names : [$page_names];
    foreach ($pages as $page) {
        if (basename($_SERVER['PHP_SELF']) == $page) {
            return 'active';
        }
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
        <div class="menu">
            <div class="menu-item <?php echo is_active('index.php'); ?>">
                <a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            </div>
            
            <div class="menu-section-title">Gestão de Pessoas</div>
            <div class="menu-item <?php echo is_active(['listagem_funcionarios.php', 'cadastro_usuario.php', 'editar_usuario.php']); ?>">
                <a href="listagem_funcionarios.php"><i class="fas fa-users-cog"></i><span>Funcionários</span></a>
            </div>
            <div class="menu-item <?php echo is_active('listagem_alunos_responsaveis.php'); ?>">
                <a href="listagem_alunos_responsaveis.php"><i class="fas fa-user-friends"></i><span>Alunos e Responsáveis</span></a>
            </div>
            <div class="menu-item <?php echo is_active('atestados_funcionarios.php'); ?>">
                <a href="atestados_funcionarios.php"><i class="fas fa-file-medical"></i><span>Atestados Funcionários</span></a>
            </div>

            <div class="menu-section-title">Visão Geral da Creche</div>
            <div class="menu-item <?php echo is_active('visualizar_turmas.php'); ?>">
                <a href="visualizar_turmas.php"><i class="fas fa-chalkboard"></i><span>Turmas e Salas</span></a>
            </div>
             <div class="menu-item <?php echo is_active('visualizar_diario_bordo.php'); ?>">
                <a href="visualizar_diario_bordo.php"><i class="fas fa-book-open"></i><span>Diário de Bordo</span></a>
            </div>
            <div class="menu-item <?php echo is_active('avisos_diretor.php'); ?>">
                <a href="avisos_diretor.php"><i class="fas fa-bell"></i><span>Avisos</span></a>
            </div>
            
            <div class="menu-section-title">Relatórios Gerenciais</div>
            <div class="menu-item <?php echo is_active('relatorio_frequencia_consolidado.php'); ?>">
                <a href="relatorio_frequencia_consolidado.php"><i class="fas fa-chart-bar"></i><span>Frequência</span></a>
            </div>
            <div class="menu-item <?php echo is_active('relatorio_ocorrencias.php'); ?>">
                <a href="relatorio_ocorrencias.php"><i class="fas fa-file-alt"></i><span>Ocorrências</span></a>
            </div>
            <div class="menu-item <?php echo is_active('relatorio_matriculas.php'); ?>">
                <a href="relatorio_matriculas.php"><i class="fas fa-user-check"></i><span>Matrículas</span></a>
            </div>
             <div class="menu-item <?php echo is_active('relatorio_saude_atestados.php'); ?>">
                <a href="relatorio_saude_atestados.php"><i class="fas fa-heartbeat"></i><span>Saúde (Alunos)</span></a>
            </div>
            <div class="menu-item <?php echo is_active('relatorio_atividades_professores.php'); ?>">
                <a href="relatorio_atividades_professores.php"><i class="fas fa-tasks"></i><span>Atividades (Prof.)</span></a>
            </div>
        </div>
        <div class="logout">
            <a href="../tela_login/logout.php"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
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
                echo '<div class="alert success" style="margin-bottom: 20px;">' . htmlspecialchars($_SESSION['mensagem_sucesso']) . '</div>';
                unset($_SESSION['mensagem_sucesso']);
            }
            if (isset($_SESSION['mensagem_erro'])) {
                echo '<div class="alert error" style="margin-bottom: 20px;">' . htmlspecialchars($_SESSION['mensagem_erro']) . '</div>';
                unset($_SESSION['mensagem_erro']);
            }
            ?>