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
// Se o utilizador não estiver logado ou não for um Responsável, redireciona para o login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["perfil"] !== 'Responsavel') {
    header("location: ../../tela_login/index.php");
    exit;
}

// Inclui o ficheiro de conexão com o banco de dados
require_once PROJECT_ROOT . '/conexao.php';

// Pega os dados do utilizador da sessão
$id_responsavel_logado = $_SESSION['id_usuario'];
$nome_responsavel_logado = $_SESSION['nome_completo'];

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo isset($page_title) ? $page_title . ' - Portal do Responsável' : 'Portal do Responsável'; ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="CSS/Style.css"/>
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
          <a href="index.php"><i class="fas fa-home"></i><span>Página Inicial</span></a>
        </div>
        <div class="menu-item <?php echo is_active('perfil_crianca.php'); ?>">
          <a href="perfil_crianca.php"><i class="fas fa-child"></i><span>Perfil da Criança</span></a>
        </div>
        <div class="menu-item <?php echo is_active('avisos_responsavel.php'); ?>">
          <a href="avisos_responsavel.php"><i class="fas fa-bell"></i><span>Avisos</span></a>
        </div>
        <div class="menu-item <?php echo is_active('atestados_responsavel.php'); ?>">
          <a href="atestados_responsavel.php"><i class="fas fa-notes-medical"></i><span>Atestados</span></a>
        </div>
        <div class="menu-item <?php echo is_active('ocorrencias_responsavel.php'); ?>">
          <a href="ocorrencias_responsavel.php"><i class="fas fa-exclamation-triangle"></i><span>Ocorrências</span></a>
        </div>
         <div class="menu-item has-submenu open">
            <i class="fas fa-file-alt"></i>
            <span>Relatórios</span>
        </div>
        <div class="submenu open">
            <div class="menu-item <?php echo is_active('relatorio_frequencia.php'); ?>">
                <a href="relatorio_frequencia.php"><i class="fas fa-calendar-check"></i><span>Frequência</span></a>
            </div>
            <div class="menu-item <?php echo is_active('relatorio_rotina.php'); ?>">
                <a href="relatorio_rotina.php"><i class="fas fa-clipboard-list"></i><span>Rotina Diária</span></a>
            </div>
            <div class="menu-item <?php echo is_active('relatorios_responsavel.php'); ?>">
                <a href="relatorios_responsavel.php"><i class="fas fa-chart-line"></i><span>Desenvolvimento</span></a>
            </div>
        </div>
      </div>
      <div class="menu-item">
        <a href="../tela_login/logout.php"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
      </div>
    </div>
  </div>
  <div class="main-content">
    <div class="header">
      <div class="page-title">
        <i class="<?php echo isset($page_icon) ? $page_icon : 'fas fa-home'; ?>"></i>
        <h2><?php echo isset($page_title) ? $page_title : 'Página Inicial'; ?></h2>
      </div>
      <div class="user-info">
        <div class="user-avatar"><?php echo strtoupper(substr($nome_responsavel_logado, 0, 2)); ?></div>
        <span><?php echo htmlspecialchars($nome_responsavel_logado); ?></span>
      </div>
    </div>
    <div class="content-container">