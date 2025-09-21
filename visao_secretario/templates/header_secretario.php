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
// Se o utilizador não estiver logado ou não for um Secretário, redireciona para o login
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
    <title><?php echo isset($page_title) ? $page_title . ' - Rede Educacional' : 'Rede Educacional'; ?></title>
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
            <div class="menu-section">
                <div class="menu-item <?php echo is_active('index.php'); ?>">
                    <a href="index.php"><i class="fas fa-home"></i><span>Início</span></a>
                </div>

                <div class="menu-item has-submenu open">
                    <i class="fas fa-user-cog"></i>
                    <span>Cadastros</span>
                </div>
                <div class="submenu open">
                    <div class="menu-item <?php echo is_active('Listagem_Alunos.php') || is_active('Cadastro_Alunos.php'); ?>">
                        <a href="Listagem_Alunos.php"><i class="fas fa-users"></i><span>Cadastro de Alunos</span></a>
                    </div>
                    <div class="menu-item <?php echo is_active('Listagem_Responsavel.php') || is_active('Cadastro_Responsavel.php'); ?>">
                         <a href="Listagem_Responsavel.php"><i class="fas fa-user-tie"></i><span>Cadastro de Responsável</span></a>
                    </div>
                    <div class="menu-item <?php echo is_active('Listagem_Atestado.php') || is_active('Cadastro_Atestado.php'); ?>">
                        <a href="Listagem_Atestado.php"><i class="fas fa-file-medical"></i><span>Cadastro de Atestados</span></a>
                    </div>
                    <div class="menu-item <?php echo is_active('Listagem_Turma.php') || is_active('Cadastro_Turma.php'); ?>">
                        <a href="Listagem_Turma.php"><i class="fas fa-chalkboard-teacher"></i><span>Cadastro de Turmas</span></a>
                    </div>
                    <div class="menu-item <?php echo is_active('Listagem_Sala.php') || is_active('Cadastro_Salas.php'); ?>">
                        <a href="Listagem_Sala.php"><i class="fas fa-door-open"></i><span>Cadastro de Salas</span></a>
                    </div>
                </div>
                
                <div class="menu-item has-submenu open">
                    <i class="fas fa-cogs"></i>
                    <span>Gerenciamento</span>
                </div>
                 <div class="submenu open">
                    <div class="menu-item <?php echo is_active('Troca_Turma.php'); ?>">
                        <a href="Troca_Turma.php"><i class="fas fa-exchange-alt"></i><span>Troca de Turma</span></a>
                    </div>
                    <div class="menu-item <?php echo is_active('Listagem_Ocorrencia.php') || is_active('Cadastro_Ocorrencia.php'); ?>">
                        <a href="Listagem_Ocorrencia.php"><i class="fas fa-exclamation-triangle"></i><span>Ocorrências</span></a>
                    </div>
                </div>
                
                <div class="menu-item has-submenu open">
                    <i class="fas fa-file-alt"></i>
                    <span>Relatórios</span>
                </div>
                <div class="submenu open">
                    <div class="menu-item <?php echo is_active('relatorio_vagas.php'); ?>">
                        <a href="relatorio_vagas.php"><i class="fas fa-chart-pie"></i><span>Relatório de Vagas</span></a>
                    </div>
                    <div class="menu-item <?php echo is_active('relatorio_lista_alunos.php'); ?>">
                        <a href="relatorio_lista_alunos.php"><i class="fas fa-print"></i><span>Lista de Alunos</span></a>
                    </div>
                </div>
            </div>
             <div class="menu-item <?php echo is_active('avisos_secretario.php') || is_active('Cadastro_Aviso.php'); ?>">
                <a href="avisos_secretario.php"><i class="fas fa-bell"></i><span>Avisos</span></a>
            </div>
            <div class="menu-item">
                <a href="../../tela_login/logout.php"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="page-title">
                <i class="<?php echo isset($page_icon) ? $page_icon : 'fas fa-tachometer-alt'; ?>"></i>
                <h2><?php echo isset($page_title) ? $page_title : 'Início'; ?></h2>
            </div>
            <div class="user-info">
                <div class="user-avatar">SC</div>
                <span><?php echo htmlspecialchars($nome_secretario_logado); ?></span>
            </div>
        </div>

        <div class="content-container">