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
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || strtolower($_SESSION["perfil"]) !== 'bercarista') {
    header("location: ../../tela_login/index.php");
    exit;
}

// Inclui o ficheiro de conexão com o banco de dados
require_once PROJECT_ROOT . '/conexao.php';

// Pega os dados do utilizador da sessão
$id_bercarista_logado = $_SESSION['id_usuario'];
$nome_bercarista_logado = $_SESSION['nome_completo'];

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo isset($page_title) ? $page_title . ' - Painel do Berçarista' : 'Painel do Berçarista'; ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="CSS/Css_Bercarista/Style_bercarista.css" />
</head>
<body>
  <div class="sidebar">
    <div class="logo"><i class="fas fa-child"></i> <h1>Berçário</h1></div>
    <div class="menu">
      <div class="menu-section">
        <div class="menu-item <?php echo is_active('index.php'); ?>">
          <a href="index.php"><i class="fas fa-home"></i><span>Início</span></a>
        </div>
        <div class="menu-item <?php echo is_active('registro_diario.php'); ?>">
          <a href="registro_diario.php"><i class="fas fa-clipboard-list"></i><span>Registo de Rotina</span></a>
        </div>
        <div class="menu-item <?php echo is_active('presenca_bercarista.php'); ?>">
            <a href="presenca_bercarista.php"><i class="fas fa-check-circle"></i><span>Controlo de Presença</span></a>
        </div>
        <div class="menu-item <?php echo is_active('ocorrencias_bercarista.php'); ?>">
          <a href="ocorrencias_bercarista.php"><i class="fas fa-exclamation-triangle"></i><span>Ocorrências</span></a>
        </div>
        <div class="menu-item <?php echo is_active('relatorio_diario_individual.php'); ?>">
            <a href="relatorio_diario_individual.php"><i class="fas fa-file-alt"></i><span>Relatório Diário</span></a>
        </div>
        <div class="menu-item <?php echo is_active('avisos_bercarista.php'); ?>">
          <a href="avisos_bercarista.php"><i class="fas fa-bell"></i><span>Avisos</span></a>
        </div>
        <div class="menu-item">
          <a href="../tela_login/logout.php"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
        </div>
      </div>
    </div>
  </div>
  <div class="main-content">
    <div class="header">
      <div class="page-title">
        <i class="<?php echo isset($page_icon) ? $page_icon : 'fas fa-home'; ?>"></i>
        <h2><?php echo isset($page_title) ? $page_title : 'Início'; ?></h2>
      </div>
      <div class="user-info">
        <div class="user-avatar">BE</div>
        <span><?php echo htmlspecialchars($nome_bercarista_logado); ?></span>
      </div>
    </div>
    <div class="content-container">