<?php
$page_title = 'Dashboard da Secretaria';
$page_icon = 'fas fa-tachometer-alt';
// Inclui o cabeçalho do template, que já tem a sessão e a conexão
require_once '../templates/header_secretario.php';

?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Cadastros</h3></div>
    <div class="card-body">
        <div class="shortcut-grid">
            <a href="Listagem_Alunos.php" class="shortcut-card">
                <i class="fas fa-user-graduate blue"></i>
                <span>Alunos</span>
            </a>
            <a href="Listagem_Responsavel.php" class="shortcut-card">
                <i class="fas fa-user-tie green"></i>
                <span>Responsáveis</span>
            </a>
            <a href="Listagem_Turma.php" class="shortcut-card">
                <i class="fas fa-chalkboard-teacher purple"></i>
                <span>Turmas</span>
            </a>
            <a href="Listagem_Sala.php" class="shortcut-card">
                <i class="fas fa-door-open orange"></i>
                <span>Salas</span>
            </a>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header"><h3 class="section-title">Gerenciamento</h3></div>
    <div class="card-body">
        <div class="shortcut-grid">
            <a href="avisos_secretario.php" class="shortcut-card">
                <i class="fas fa-bell red"></i>
                <span>Avisos</span>
            </a>
            <a href="Listagem_Ocorrencia.php" class="shortcut-card">
                <i class="fas fa-exclamation-triangle orange"></i>
                <span>Ocorrências</span>
            </a>
            <a href="Listagem_Atestado.php" class="shortcut-card">
                <i class="fas fa-file-medical green"></i>
                <span>Atestados</span>
            </a>
            <a href="Troca_Turma.php" class="shortcut-card">
                <i class="fas fa-exchange-alt grey"></i>
                <span>Troca de Turma</span>
            </a>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header"><h3 class="section-title">Relatórios</h3></div>
    <div class="card-body">
        <div class="shortcut-grid">
            <a href="../relatorios/relatorio_vagas.php" class="shortcut-card">
                <i class="fas fa-chart-pie blue"></i>
                <span>Vagas por Turma</span>
            </a>
            <a href="../relatorios/relatorio_lista_alunos.php" class="shortcut-card">
                <i class="fas fa-print grey"></i>
                <span>Lista de Alunos</span>
            </a>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé do template
require_once '../templates/footer_secretario.php';
?>