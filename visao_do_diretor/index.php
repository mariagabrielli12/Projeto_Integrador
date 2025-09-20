<?php
define('VIEW_ROOT', __DIR__); 
define('PROJECT_ROOT', dirname(VIEW_ROOT)); 

$page_title = 'Dashboard do Diretor';
$page_icon = 'fas fa-tachometer-alt';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA PARA OS CARDS DE RESUMO ---
$total_alunos = $conexao->query("SELECT COUNT(*) as total FROM alunos")->fetch_assoc()['total'];
$total_funcionarios = $conexao->query("SELECT COUNT(*) as total FROM usuarios WHERE id_tipo IN (2, 3, 4) AND ativo = 1")->fetch_assoc()['total'];
$total_turmas = $conexao->query("SELECT COUNT(*) as total FROM turmas")->fetch_assoc()['total'];
$ocorrencias_pendentes = $conexao->query("SELECT COUNT(*) as total FROM ocorrencias WHERE status = 'Pendente'")->fetch_assoc()['total'];

?>

<div class="summary-cards">
    <a href="listagem_alunos_responsaveis.php" class="summary-card card-alunos">
        <h3><?php echo $total_alunos; ?></h3>
        <p>Alunos Matriculados</p>
    </a>
    <a href="listagem_funcionarios.php" class="summary-card card-funcionarios">
        <h3><?php echo $total_funcionarios; ?></h3>
        <p>Funcionários Ativos</p>
    </a>
    <a href="visualizar_turmas.php" class="summary-card card-turmas">
        <h3><?php echo $total_turmas; ?></h3>
        <p>Turmas Formadas</p>
    </a>
    <a href="relatorio_ocorrencias.php" class="summary-card card-comunicacao">
        <h3><?php echo $ocorrencias_pendentes; ?></h3>
        <p>Ocorrências Pendentes</p>
    </a>
</div>

<div class="card">
    <div class="card-header"><h3 class="section-title"><i class="fas fa-tasks"></i> Central de Ações</h3></div>
    <div class="card-body">
        
        <h4 style="margin-bottom: 15px; color: var(--primary);">Gestão de Pessoas</h4>
        <div class="summary-cards">
            <a href="cadastro_usuario.php" class="summary-card card-gestao">
                <h3><i class="fas fa-user-plus"></i></h3>
                <p>Cadastrar Funcionário</p>
            </a>
            <a href="atestados_funcionarios.php" class="summary-card card-gestao">
                <h3><i class="fas fa-file-medical"></i></h3>
                <p>Atestados de Funcionários</p>
            </a>
        </div>

        <hr style="margin: 25px 0;">

        <h4 style="margin-bottom: 15px; color: var(--primary);">Comunicação e Visão Geral</h4>
        <div class="summary-cards">
            <a href="avisos_diretor.php" class="summary-card card-comunicacao">
                <h3><i class="fas fa-bell"></i></h3>
                <p>Gerenciar Avisos</p>
            </a>
            <a href="visualizar_diario_bordo.php" class="summary-card card-turmas">
                <h3><i class="fas fa-book-open"></i></h3>
                <p>Diário de Bordo Geral</p>
            </a>
        </div>
        
        <hr style="margin: 25px 0;">

        <h4 style="margin-bottom: 15px; color: var(--primary);">Relatórios Gerenciais</h4>
        <div class="summary-cards">
            <a href="relatorio_frequencia_consolidado.php" class="summary-card card-relatorios">
                <h3><i class="fas fa-chart-bar"></i></h3>
                <p>Frequência</p>
            </a>
            <a href="relatorio_matriculas.php" class="summary-card card-relatorios">
                <h3><i class="fas fa-user-check"></i></h3>
                <p>Matrículas</p>
            </a>
            <a href="relatorio_saude_atestados.php" class="summary-card card-relatorios">
                <h3><i class="fas fa-heartbeat"></i></h3>
                <p>Saúde e Atestados</p>
            </a>
            <a href="relatorio_atividades_professores.php" class="summary-card card-relatorios">
                <h3><i class="fas fa-tasks"></i></h3>
                <p>Atividades (Prof.)</p>
            </a>
        </div>
    </div>
</div>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>