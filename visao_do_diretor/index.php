<?php
$page_title = 'Dashboard do Diretor';
$page_icon = 'fas fa-tachometer-alt';
// Inclui o cabeçalho do template, que já tem a sessão e a conexão
require_once 'templates/header_diretor.php';
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
    <div class="card-header"><h3 class="section-title">Visão Geral da Creche</h3></div>
    <div class="card-body">
        <div class="shortcut-grid">
            <a href="visualizar_turmas.php" class="shortcut-card">
                <i class="fas fa-chalkboard purple"></i>
                <span>Turmas e Salas</span>
            </a>
            <a href="visualizar_diario_bordo.php" class="shortcut-card">
                <i class="fas fa-book-open teal"></i>
                <span>Diário de Bordo</span>
            </a>
            <a href="avisos_diretor.php" class="shortcut-card">
                <i class="fas fa-bell red"></i>
                <span>Avisos</span>
            </a>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header"><h3 class="section-title">Relatórios Gerenciais</h3></div>
    <div class="card-body">
        <div class="shortcut-grid">
            <a href="relatorio_frequencia_consolidado.php" class="shortcut-card">
                <i class="fas fa-chart-bar blue"></i>
                <span>Frequência</span>
            </a>
            <a href="relatorio_ocorrencias.php" class="shortcut-card">
                <i class="fas fa-file-alt red"></i>
                <span>Ocorrências</span>
            </a>
            <a href="relatorio_matriculas.php" class="shortcut-card">
                <i class="fas fa-user-check green"></i>
                <span>Matrículas</span>
            </a>
            <a href="relatorio_saude_atestados.php" class="shortcut-card">
                <i class="fas fa-heartbeat orange"></i>
                <span>Saúde (Alunos)</span>
            </a>
            <a href="relatorio_atividades_professores.php" class="shortcut-card">
                <i class="fas fa-tasks teal"></i>
                <span>Atividades (Prof.)</span>
            </a>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>