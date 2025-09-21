<?php
$page_title = 'Dashboard do Berçarista';
$page_icon = 'fas fa-tachometer-alt';
// Inclui o cabeçalho do template, que já tem a sessão e a conexão
require_once 'templates/header_bercarista.php';
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Rotina Diária</h3></div>
    <div class="card-body">
        <div class="shortcut-grid">
            <a href="registro_diario.php" class="shortcut-card blue-bg">
                <i class="fas fa-clipboard-list"></i>
                <span>Registro de Rotina</span>
            </a>
            <a href="presenca_bercarista.php" class="shortcut-card green-bg">
                <i class="fas fa-check-circle"></i>
                <span>Controle de Presença</span>
            </a>
            <a href="ocorrencias_bercarista.php" class="shortcut-card orange-bg">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Ocorrências</span>
            </a>
            <a href="relatorio_diario_individual.php" class="shortcut-card purple-bg">
                <i class="fas fa-file-alt"></i>
                <span>Relatório Diário</span>
            </a>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header"><h3 class="section-title">Gestão de Crianças</h3></div>
    <div class="card-body">
        <div class="shortcut-grid">
            <a href="perfil_crianca_bercarista.php" class="shortcut-card teal-bg">
                <i class="fas fa-user-friends"></i>
                <span>Perfis das Crianças</span>
            </a>
            <a href="avisos_bercarista.php" class="shortcut-card red-bg">
                <i class="fas fa-bell"></i>
                <span>Avisos</span>
            </a>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé do template
require_once 'templates/footer_bercarista.php';
?>