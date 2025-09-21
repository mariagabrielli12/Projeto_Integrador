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
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["perfil"] !== 'Secretario') {
    header("location: ../../tela_login/index.php");
    exit;
}

// Inclui o ficheiro de conexão com o banco de dados
require_once PROJECT_ROOT . '/conexao.php';

// Pega os dados do utilizador da sessão
$id_secretario_logado = $_SESSION['id_usuario'];
$nome_secretario_logado = $_SESSION['nome_completo'];

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
    <title><?php echo isset($page_title) ? $page_title . ' - Portal da Secretaria' : 'Portal da Secretaria'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS_Secretario/Style_Secretario.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <h1>Rede Educacional</h1>
        </div>
        <div class="menu">
            <div class="menu-item <?php echo is_active('index.php'); ?>">
                <a href="index.php"><i class="fas fa-home"></i><span>Início</span></a>
            </div>

            <div class="menu-section-title">Cadastros</div>
            <div class="menu-item <?php echo is_active(['Listagem_Alunos.php', 'Cadastro_Alunos.php']); ?>">
                <a href="Listagem_Alunos.php"><i class="fas fa-user-graduate"></i><span>Alunos</span></a>
            </div>
            <div class="menu-item <?php echo is_active(['Listagem_Responsavel.php', 'Cadastro_Responsavel.php']); ?>">
                 <a href="Listagem_Responsavel.php"><i class="fas fa-user-tie"></i><span>Responsáveis</span></a>
            </div>
            <div class="menu-item <?php echo is_active(['Listagem_Turma.php', 'Cadastro_Turma.php']); ?>">
                <a href="Listagem_Turma.php"><i class="fas fa-chalkboard-teacher"></i><span>Turmas</span></a>
            </div>
            <div class="menu-item <?php echo is_active(['Listagem_Sala.php', 'Cadastro_Salas.php']); ?>">
                <a href="Listagem_Sala.php"><i class="fas fa-door-open"></i><span>Salas</span></a>
            </div>

            <div class="menu-section-title">Gerenciamento</div>
            <div class="menu-item <?php echo is_active(['avisos_secretario.php', 'Cadastro_Aviso.php']); ?>">
                <a href="avisos_secretario.php"><i class="fas fa-bell"></i><span>Avisos</span></a>
            </div>
             <div class="menu-item <?php echo is_active(['Listagem_Ocorrencia.php', 'Cadastro_Ocorrencia.php']); ?>">
                <a href="Listagem_Ocorrencia.php"><i class="fas fa-exclamation-triangle"></i><span>Ocorrências</span></a>
            </div>
            <div class="menu-item <?php echo is_active(['Listagem_Atestado.php', 'Cadastro_Atestado.php']); ?>">
                <a href="Listagem_Atestado.php"><i class="fas fa-file-medical"></i><span>Atestados</span></a>
            </div>
            <div class="menu-item <?php echo is_active('Troca_Turma.php'); ?>">
                <a href="Troca_Turma.php"><i class="fas fa-exchange-alt"></i><span>Troca de Turma</span></a>
            </div>
            
            <div class="menu-section-title">Relatórios</div>
            <div class="menu-item <?php echo is_active('relatorio_vagas.php'); ?>">
                <a href="relatorio_vagas.php"><i class="fas fa-chart-pie"></i><span>Vagas por Turma</span></a>
            </div>
            <div class="menu-item <?php echo is_active('relatorio_lista_alunos.php'); ?>">
                <a href="relatorio_lista_alunos.php"><i class="fas fa-print"></i><span>Lista de Alunos</span></a>
            </div>
        </div>
        <div class="logout">
            <a href="../../tela_login/logout.php"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="page-title">
                <i class="<?php echo isset($page_icon) ? $page_icon : 'fas fa-tachometer-alt'; ?>"></i>
                <h2><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h2>
            </div>
            <div class="user-info">
                <div class="user-avatar">SC</div>
                <span><?php echo htmlspecialchars($nome_secretario_logado); ?></span>
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