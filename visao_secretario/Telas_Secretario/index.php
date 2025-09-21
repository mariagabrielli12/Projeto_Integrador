<?php
$page_title = 'Dashboard da Secretaria';
$page_icon = 'fas fa-tachometer-alt';

// Inclui o cabeçalho do template
require_once '../templates/header_secretario.php';

// Busca os 3 avisos mais recentes
$avisos_recentes = $conexao->query(
    "SELECT titulo, descricao, data_aviso, categoria 
     FROM avisos 
     ORDER BY data_aviso DESC 
     LIMIT 3"
);
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Cadastros Principais</h3></div>
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
    <div class="card-header"><h3 class="section-title">Avisos Recentes</h3></div>
    <div class="card-body">
        <?php if ($avisos_recentes && $avisos_recentes->num_rows > 0): ?>
            <?php while($aviso = $avisos_recentes->fetch_assoc()): ?>
                <div class="notice-card">
                    <h4 class="card-title">
                        <i class="fas fa-bell"></i> <?php echo htmlspecialchars($aviso['titulo']); ?>
                    </h4>
                    <p class="card-meta">
                        <span><i class="far fa-clock"></i> <?php echo date("d/m/Y", strtotime($aviso['data_aviso'])); ?></span>
                        <span><i class="fas fa-tag"></i> Categoria: <?php echo htmlspecialchars($aviso['categoria']); ?></span>
                    </p>
                    <p class="card-text"><?php echo htmlspecialchars($aviso['descricao']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum aviso recente encontrado.</p>
        <?php endif; ?>
        
        <div class="card-actions" style="text-align: right; margin-top: 15px;">
            <a href="avisos_secretario.php" class="btn btn-primary">Gerenciar Avisos <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</div>
<?php
// Inclui o rodapé do template
require_once '../templates/footer_secretario.php';
?>