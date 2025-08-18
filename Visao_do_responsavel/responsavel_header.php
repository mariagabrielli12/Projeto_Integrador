<?php
// Inicia a sessão para controle de login
session_start();

// Inclui a conexão com o banco de dados
require_once __DIR__ . '/../conexao.php';

// Simulação de dados do responsável logado e seu filho
// Em um sistema real, isso viria da sessão após o login
$responsavel_logado_id = 3; // ID do usuário do responsável
$nome_responsavel_logado = "Ana Paula";
$aluno_id_associado = 1; // ID do aluno associado a este responsável
$nome_aluno_associado = "Lucas Oliveira";

// Função para marcar o item de menu ativo
function is_active_responsavel($page_name) {
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
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Portal do Responsável' : 'Portal do Responsável'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/Style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <h1>Rede Educacional</h1>
        </div>
        <div class="menu">
            <div class="menu-section">
                <div class="menu-item <?php echo is_active_responsavel('index.php'); ?>">
                    <a href="index.php"><i class="fas fa-home"></i><span>Página Inicial</span></a>
                </div>
                <div class="menu-item <?php echo is_active_responsavel('perfil_crianca.php'); ?>">
                    <a href="perfil_crianca.php"><i class="fas fa-child"></i><span>Perfil da Criança</span></a>
                </div>
                <div class="menu-item <?php echo is_active_responsavel('relatorios_responsavel.php'); ?>">
                    <a href="relatorios_responsavel.php"><i class="fas fa-file-alt"></i><span>Relatórios</span></a>
                </div>
                <div class="menu-item <?php echo is_active_responsavel('avisos_responsavel.php'); ?>">
                    <a href="avisos_responsavel.php"><i class="fas fa-bell"></i><span>Avisos</span></a>
                </div>
                <div class="menu-item <?php echo is_active_responsavel('atestados_responsavel.php'); ?>">
                    <a href="atestados_responsavel.php"><i class="fas fa-notes-medical"></i><span>Atestados</span></a>
                </div>
                <div class="menu-item <?php echo is_active_responsavel('ocorrencias_responsavel.php'); ?>">
                    <a href="ocorrencias_responsavel.php"><i class="fas fa-exclamation-triangle"></i><span>Ocorrências</span></a>
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
                <h2><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Página Inicial'; ?></h2>
            </div>
            <div class="user-info">
                <div class="user-avatar">RA</div>
                <span><?php echo htmlspecialchars($nome_responsavel_logado); ?></span>
            </div>
        </div>
        <div class="content-container">
        <?php
        // Bloco para exibir mensagens de feedback da sessão
        if (isset($_SESSION['mensagem_sucesso'])) {
            echo '<div class="alert success">' . htmlspecialchars($_SESSION['mensagem_sucesso']) . '</div>';
            unset($_SESSION['mensagem_sucesso']);
        }
        if (isset($_SESSION['mensagem_erro'])) {
            echo '<div class="alert error">' . htmlspecialchars($_SESSION['mensagem_erro']) . '</div>';
            unset($_SESSION['mensagem_erro']);
        }
        ?>