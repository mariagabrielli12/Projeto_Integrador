<?php
define('ROOT_PATH', dirname(__DIR__)); 
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

$page_title = 'Dashboard';
$page_icon = 'fas fa-home';
$breadcrumb = 'Portal do Professor > Dashboard';

// Busca a próxima atividade na agenda para este professor
$proxima_atividade = null;
$stmt_agenda = $conexao->prepare(
    "SELECT a.titulo, a.data_atividade, t.nome_turma 
     FROM atividades a
     JOIN turmas t ON a.id_turma = t.id_turma
     WHERE a.id_professor = ? AND a.data_atividade >= CURDATE()
     ORDER BY a.data_atividade ASC
     LIMIT 1"
);
$stmt_agenda->bind_param("i", $id_professor_logado);
$stmt_agenda->execute();
$result_agenda = $stmt_agenda->get_result();
if ($result_agenda->num_rows > 0) {
    $proxima_atividade = $result_agenda->fetch_assoc();
}
$stmt_agenda->close();

$result_avisos = $conexao->query(
    "SELECT titulo, descricao FROM avisos 
     WHERE destinatario IN ('GERAL', 'FUNCIONARIOS') 
     ORDER BY data_aviso DESC LIMIT 3"
);
if ($result_avisos) {
    while($row = $result_avisos->fetch_assoc()){
        $avisos[] = $row;
    }
}
?>

<div class="welcome-banner">
    <h3>Bem-vindo(a), <?php echo htmlspecialchars($nome_professor_logado); ?></h3>
    <p>
       
        <?php if ($proxima_atividade): ?>
            Próxima atividade: <?php echo htmlspecialchars($proxima_atividade['titulo']); ?> (<?php echo htmlspecialchars($proxima_atividade['nome_turma']); ?>) em <?php echo date('d/m H:i', strtotime($proxima_atividade['data_atividade'])); ?>
        <?php else: ?>
            Nenhuma atividade agendada.
        <?php endif; ?>
    </p>
</div>

<div class="card">
    <div class="card-header"><h3 class="section-title">Agenda e Avisos</h3></div>
    <div class="card-body">
        <div class="dashboard-grid">
            <div class="dashboard-card calendar-card">
                <h4><i class="fas fa-calendar-day"></i> Agenda do Dia</h4>
                <div class="calendar-events">
                    <?php if ($proxima_atividade && date('Y-m-d', strtotime($proxima_atividade['data_atividade'])) == date('Y-m-d')): ?>
                        <div class="event"><span class="event-time"><?php echo date("H:i", strtotime($proxima_atividade['data_atividade'])); ?></span> <span class="event-title"><?php echo htmlspecialchars($proxima_atividade['titulo']); ?></span></div>
                    <?php else: ?>
                        <div class="event"><span class="event-title">Nenhuma atividade específica para hoje.</span></div>
                    <?php endif; ?>
                </div>
                <a href="plano_atividades.php" class="view-all">Ver agenda completa</a>
            </div>

            <div class="dashboard-card alerts-card">
                <h4><i class="fas fa-bell"></i> Avisos Recentes da Secretaria</h4>
                <div class="alert-list">
                    <?php if (!empty($avisos)): ?>
                        <?php foreach($avisos as $aviso): ?>
                            <div class="alert"><i class="fas fa-info-circle"></i> <span><strong><?php echo htmlspecialchars($aviso['titulo']); ?>:</strong> <?php echo htmlspecialchars($aviso['descricao']); ?></span></div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert"><i class="fas fa-check-circle"></i> <span>Nenhum aviso novo.</span></div>
                    <?php endif; ?>
                </div>
                <a href="comunicados_professor.php" class="view-all">Ver todos os comunicados</a>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header"><h3 class="section-title">Acesso Rápido</h3></div>
    <div class="card-body">
        <div class="shortcut-grid">
            <a href="minhas_turmas.php" class="shortcut-card">
                <i class="fas fa-child" style="color: #3498db;"></i>
                <span>Minhas Turmas</span>
            </a>
            <a href="desenvolvimento_aluno.php" class="shortcut-card">
                <i class="fas fa-chart-line" style="color: #2ecc71;"></i>
                <span>Acompanhamento</span>
            </a>
            <a href="diario_bordo.php" class="shortcut-card">
                <i class="fas fa-book" style="color: #9b59b6;"></i>
                <span>Diário de Bordo</span>
            </a>
            <a href="ocorrencias_professor.php" class="shortcut-card">
                <i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i>
                <span>Ocorrências</span>
            </a>
            <a href="plano_atividades.php" class="shortcut-card">
                <i class="fas fa-calendar-day" style="color: #1abc9c;"></i>
                <span>Plano de Atividades</span>
            </a>
            <a href="atividades_ludicas.php" class="shortcut-card">
                <i class="fas fa-puzzle-piece" style="color: #e74c3c;"></i>
                <span>Atividades Lúdicas</span>
            </a>
            <a href="comunicados_professor.php" class="shortcut-card">
                <i class="fas fa-comment-dots" style="color: #7f8c8d;"></i>
                <span>Comunicados</span>
            </a>
            <a href="relatorios_professor.php" class="shortcut-card">
                <i class="fas fa-file-alt" style="color: #3498db;"></i>
                <span>Relatórios</span>
            </a>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer_professor.php';
?>