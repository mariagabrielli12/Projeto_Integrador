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
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["perfil"] !== 'Responsavel') {
    header("location: ../../tela_login/index.php");
    exit;
}

// Inclui o ficheiro de conexão com o banco de dados
require_once PROJECT_ROOT . '/conexao.php';

// Pega os dados do utilizador da sessão
$id_responsavel_logado = $_SESSION['id_usuario'];
$nome_responsavel_logado = $_SESSION['nome_completo'];

// --- LÓGICA CENTRAL: GERENCIAR MÚLTIPLOS FILHOS ---
$stmt_alunos = $conexao->prepare("SELECT a.id_aluno, a.nome_completo FROM alunos a JOIN alunos_responsaveis ar ON a.id_aluno = ar.id_aluno WHERE ar.id_responsavel = ? ORDER BY a.nome_completo");
$stmt_alunos->bind_param("i", $id_responsavel_logado);
$stmt_alunos->execute();
$alunos_associados = $stmt_alunos->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_alunos->close();

// Define qual aluno está ativo
$id_aluno_logado = 0;
$nome_aluno_logado = 'Nenhuma criança associada';

if (count($alunos_associados) > 0) {
    $aluno_id_url = isset($_GET['aluno_id']) ? (int)$_GET['aluno_id'] : 0;
    $aluno_valido_na_url = false;
    foreach ($alunos_associados as $aluno) {
        if ($aluno['id_aluno'] == $aluno_id_url) {
            $aluno_valido_na_url = true;
            break;
        }
    }

    if ($aluno_valido_na_url) {
        $id_aluno_logado = $aluno_id_url;
        $_SESSION['id_aluno_selecionado'] = $id_aluno_logado;
    } elseif (isset($_SESSION['id_aluno_selecionado']) && in_array($_SESSION['id_aluno_selecionado'], array_column($alunos_associados, 'id_aluno'))) {
        $id_aluno_logado = $_SESSION['id_aluno_selecionado'];
    } else {
        $id_aluno_logado = $alunos_associados[0]['id_aluno'];
        $_SESSION['id_aluno_selecionado'] = $id_aluno_logado;
    }

    foreach($alunos_associados as $aluno) {
        if ($aluno['id_aluno'] == $id_aluno_logado) {
            $nome_aluno_logado = $aluno['nome_completo'];
            break;
        }
    }
}

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
    <ul class="menu">
      <li class="menu-item <?php echo is_active('index.php'); ?>">
        <a href="index.php"><i class="fas fa-home"></i><span>Página Inicial</span></a>
      </li>
      <li class="menu-item <?php echo is_active('perfil_crianca.php'); ?>">
        <a href="perfil_crianca.php"><i class="fas fa-child"></i><span>Perfil da Criança</span></a>
      </li>
      <li class="menu-item <?php echo is_active('avisos_responsavel.php'); ?>">
        <a href="avisos_responsavel.php"><i class="fas fa-bell"></i><span>Avisos</span></a>
      </li>
      <li class="menu-item <?php echo is_active(['atestados_responsavel.php', 'detalhe_atestado.php']); ?>">
        <a href="atestados_responsavel.php"><i class="fas fa-notes-medical"></i><span>Atestados</span></a>
      </li>
      <li class="menu-item <?php echo is_active('ocorrencias_responsavel.php'); ?>">
        <a href="ocorrencias_responsavel.php"><i class="fas fa-exclamation-triangle"></i><span>Ocorrências</span></a>
      </li>
      <li class="menu-item has-submenu open">
          <i class="fas fa-file-alt"></i>
          <span>Relatórios</span>
      </li>
      <div class="submenu open">
          <li class="menu-item <?php echo is_active('relatorio_frequencia.php'); ?>">
              <a href="relatorio_frequencia.php"><i class="fas fa-calendar-check"></i><span>Frequência</span></a>
          </li>
          <li class="menu-item <?php echo is_active('relatorio_rotina.php'); ?>">
              <a href="relatorio_rotina.php"><i class="fas fa-clipboard-list"></i><span>Rotina Diária</span></a>
          </li>
          <li class="menu-item <?php echo is_active(['relatorios_responsavel.php', 'detalhe_relatorio.php']); ?>">
              <a href="relatorios_responsavel.php"><i class="fas fa-chart-line"></i><span>Desenvolvimento</span></a>
          </li>
      </div>
    </ul>
    <div class="logout">
            <a href="../tela_login/logout.php" style="color:inherit; text-decoration:none;"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
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
    <?php if (count($alunos_associados) > 1): ?>
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-body" style="display: flex; align-items: center; gap: 15px; padding: 15px;">
                <label for="aluno_select" style="margin-bottom: 0;"><b>Visualizando dados de:</b></label>
                <select id="aluno_select" class="form-control" onchange="if (this.value) window.location.href = window.location.pathname + this.value;">
                    <?php foreach ($alunos_associados as $aluno): ?>
                        <option value="?aluno_id=<?php echo $aluno['id_aluno']; ?>" <?php echo ($id_aluno_logado == $aluno['id_aluno']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($aluno['nome_completo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>