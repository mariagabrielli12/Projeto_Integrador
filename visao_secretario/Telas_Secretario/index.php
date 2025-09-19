<?php
// Define o título e o ícone da página
$page_title = 'Dashboard do Secretário';
$page_icon = 'fas fa-tachometer-alt';

// Inclui o cabeçalho do template
require_once '../templates/header_secretario.php';

// Busca os 3 avisos mais recentes no banco de dados (QUERY ATUALIZADA)
$avisos_recentes = $conexao->query(
    "SELECT titulo, descricao, data_aviso, categoria 
     FROM avisos 
     ORDER BY data_aviso DESC 
     LIMIT 3"
);

?>

<div class="dashboard-container">
    <div class="card">
        <div class="card-header">
            <h3 class="section-title" style="font-size: 14px; padding-bottom: 5px;">Ações Rápidas</h3>
        </div>
        <div class="card-body shortcut-grid">
            <a href="Listagem_Alunos.php" class="shortcut-card" title="Gerenciar alunos">
                <i class="fas fa-users"></i>
                <span>Cadastro de Alunos</span>
            </a>
            <a href="Listagem_Responsavel.php" class="shortcut-card" title="Gerenciar responsáveis">
                <i class="fas fa-user-tie"></i>
                <span>Cadastro de Responsável</span>
            </a>
            <a href="Listagem_Atestado.php" class="shortcut-card" title="Gerenciar atestados">
                <i class="fas fa-file-medical"></i>
                <span>Cadastro de Atestados</span>
            </a>
            <a href="Listagem_Turma.php" class="shortcut-card" title="Gerenciar turmas">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Cadastro de Turmas</span>
            </a>
            <a href="Listagem_Sala.php" class="shortcut-card" title="Gerenciar salas">
                <i class="fas fa-door-open"></i>
                <span>Cadastro de Salas</span>
            </a>
            <a href="Listagem_Ocorrencia.php" class="shortcut-card" title="Visualizar ocorrências">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Ocorrências</span>
            </a>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <h3 style="font-size: 14px; padding-bottom: 5px;">Avisos Recentes</h3>
        </div>
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
            
            <div class="card-actions">
                <a href="avisos_secretario.php" class="btn btn-primary">Ver Todos os Avisos <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>
<?php
// Inclui o rodapé do template
require_once '../templates/footer_secretario.php';
?>