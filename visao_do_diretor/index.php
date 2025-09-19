<?php
define('VIEW_ROOT', __DIR__); 
define('PROJECT_ROOT', dirname(VIEW_ROOT)); 

$page_title = 'Dashboard do Diretor';
$page_icon = 'fas fa-tachometer-alt';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DO DASHBOARD ---
$total_alunos = $conexao->query("SELECT COUNT(*) as total FROM alunos")->fetch_assoc()['total'];
$total_funcionarios = $conexao->query("SELECT COUNT(*) as total FROM usuarios WHERE id_tipo IN (2, 3, 4) AND ativo = 1")->fetch_assoc()['total'];
$total_turmas = $conexao->query("SELECT COUNT(*) as total FROM turmas")->fetch_assoc()['total'];
$avisos_recentes = $conexao->query("SELECT titulo, destinatario FROM avisos ORDER BY data_aviso DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
?>

<div class="summary-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
    </div>

<div class="card">
    <div class="card-header"><h3 class="section-title">Atalhos Rápidos</h3></div>
    <div class="card-body">
        <div class="shortcut-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <a href="cadastro_usuario.php" class="shortcut-card" style="text-decoration: none; color: inherit; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <i class="fas fa-user-plus fa-2x" style="color: #3498db;"></i>
                <p style="margin-top: 10px;">Cadastrar Funcionário</p>
            </a>
            <a href="listagem_funcionarios.php" class="shortcut-card" style="text-decoration: none; color: inherit; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <i class="fas fa-users-cog fa-2x" style="color: #2ecc71;"></i>
                <p style="margin-top: 10px;">Gerir Funcionários</p>
            </a>
            <a href="visualizar_turmas.php" class="shortcut-card" style="text-decoration: none; color: inherit; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <i class="fas fa-chalkboard fa-2x" style="color: #f1c40f;"></i>
                <p style="margin-top: 10px;">Ver Turmas</p>
            </a>
            <a href="avisos_diretor.php" class="shortcut-card" style="text-decoration: none; color: inherit; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <i class="fas fa-bell fa-2x" style="color: #e74c3c;"></i>
                <p style="margin-top: 10px;">Publicar Aviso</p>
            </a>
             <a href="relatorio_frequencia_consolidado.php" class="shortcut-card" style="text-decoration: none; color: inherit; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <i class="fas fa-chart-bar fa-2x" style="color: #9b59b6;"></i>
                <p style="margin-top: 10px;">Relatório de Frequência</p>
            </a>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header"><h3 class="section-title">Últimos Avisos Publicados</h3></div>
    <div class="card-body">
        <?php foreach ($avisos_recentes as $aviso): ?>
            <div class="notice-card" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 10px;">
                <h4 class="card-title" style="font-size: 1.1em; margin-bottom: 5px;">
                    <i class="fas fa-bullhorn"></i> <?php echo htmlspecialchars($aviso['titulo']); ?>
                    <span class="status-badge active" style="font-size: 0.7em; margin-left: 10px; vertical-align: middle;"><?php echo htmlspecialchars($aviso['destinatario']); ?></span>
                </h4>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>