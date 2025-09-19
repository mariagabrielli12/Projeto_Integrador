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
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["perfil"] !== 'Professor') {
    header("location: ../../tela_login/index.php");
    exit;
}

// Inclui o ficheiro de conexão com o banco de dados
require_once(PROJECT_ROOT . '/conexao.php'); 

// Pega os dados do utilizador da sessão usando os nomes do NOVO banco de dados
$id_professor_logado = $_SESSION['id_usuario'];
$nome_professor_logado = $_SESSION['nome_completo'];

// Função para verificar qual item do menu deve estar ativo
function is_active($page_name) {
    // Permite que múltiplos nomes de página ativem o mesmo menu
    $page_names = is_array($page_name) ? $page_name : [$page_name];
    foreach ($page_names as $name) {
        if (basename($_SERVER['PHP_SELF']) == $name) {
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
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Portal do Professor' : 'Portal do Professor'; ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="Css/style_professor.css">
  <?php
  if (isset($extra_css)) {
      echo $extra_css;
  }
  ?>
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <i class="fas fa-graduation-cap"></i>
      <h1>Rede Educacional</h1>
    </div>
    <div class="menu">
      <div class="menu-section">
        <div class="menu-item <?php echo is_active('tela_principal_professor.php'); ?>">
          <a href="tela_principal_professor.php"><i class="fas fa-home"></i><span>Início</span></a>
        </div>
        <div class="menu-item has-submenu">
          <i class="fas fa-calendar-alt"></i><span>Planeamento</span>
        </div>
        <div class="submenu">
          <a href="plano_atividades.php"><div class="menu-item <?php echo is_active('plano_atividades.php'); ?>"><i class="fas fa-calendar-day"></i><span>Plano de Atividades</span></div></a>
          <a href="atividades_ludicas.php"><div class="menu-item <?php echo is_active('atividades_ludicas.php'); ?>"><i class="fas fa-puzzle-piece"></i><span>Atividades Lúdicas</span></div></a>
        </div>
        <div class="menu-item has-submenu">
          <i class="fas fa-child"></i><span>Turmas</span>
        </div>
        <div class="submenu">
          <a href="minhas_turmas.php"><div class="menu-item <?php echo is_active(['minhas_turmas.php', 'detalhes_turma.php', 'perfil_aluno.php', 'gerenciar_turma.php']); ?>"><i class="fas fa-baby-carriage"></i><span>Minhas Turmas</span></div></a>
        </div>
        <div class="menu-item has-submenu">
          <i class="fas fa-chart-line"></i><span>Acompanhamento</span>
        </div>
        <div class="submenu">
          <a href="desenvolvimento_aluno.php"><div class="menu-item <?php echo is_active(['desenvolvimento_aluno.php', 'registrar_observacao.php']); ?>"><i class="fas fa-notes-medical"></i><span>Desenvolvimento</span></div></a>
          <a href="relatorios_professor.php"><div class="menu-item <?php echo is_active('relatorios_professor.php'); ?>"><i class="fas fa-file-alt"></i><span>Relatórios</span></div></a>
        </div>
        <div class="menu-item <?php echo is_active(['comunicados_professor.php', 'criar_comunicado.php']); ?>">
          <a href="comunicados_professor.php"><i class="fas fa-comment-dots"></i><span>Comunicados</span></a>
        </div>
        <div class="menu-item <?php echo is_active('diario_bordo.php'); ?>">
            <a href="diario_bordo.php"><i class="fas fa-book"></i><span>Diário de Bordo</span></a>
        </div>
        <div class="menu-item <?php echo is_active('ocorrencias_professor.php'); ?>">
          <a href="ocorrencias_professor.php"><i class="fas fa-exclamation-triangle"></i><span>Ocorrências</span></a>
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
        <i class="<?php echo isset($page_icon) ? htmlspecialchars($page_icon) : 'fas fa-home'; ?>"></i>
        <h2><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard'; ?></h2>
      </div>
      <div class="user-info">
        <div class="user-avatar">PR</div>
        <span><?php echo htmlspecialchars($nome_professor_logado); ?></span>
      </div>
    </div>
    <div class="content-container">
      <?php
        if (isset($_SESSION['mensagem_sucesso'])) {
            echo '<div class="alert success">' . htmlspecialchars($_SESSION['mensagem_sucesso']) . '</div>';
            unset($_SESSION['mensagem_sucesso']);
        }
        if (isset($_SESSION['mensagem_erro'])) {
            echo '<div class="alert error">' . htmlspecialchars($_SESSION['mensagem_erro']) . '</div>';
            unset($_SESSION['mensagem_erro']);
        }
      ?>