<?php
$page_title = 'Página Inicial';
$page_icon = 'fas fa-home';
require_once '../templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS ---
// Contar ocorrências recentes do aluno associado
$query_ocorrencias = "SELECT COUNT(id) AS total FROM ocorrencias WHERE aluno_id = ?";
$stmt_ocorrencias = $conexao->prepare($query_ocorrencias);
$stmt_ocorrencias->bind_param("i", $aluno_id_associado);
$stmt_ocorrencias->execute();
$total_ocorrencias = $stmt_ocorrencias->get_result()->fetch_assoc()['total'];
$stmt_ocorrencias->close();

// Buscar os 2 avisos mais recentes
$query_avisos = "SELECT titulo, conteudo, data_publicacao FROM comunicados ORDER BY data_publicacao DESC LIMIT 2";
$avisos = $conexao->query($query_avisos)->fetch_all(MYSQLI_ASSOC);
// --- FIM DA LÓGICA ---
?>

<div class="summary-cards">
    <a href="perfil_crianca.php" class="summary-card">
        <div class="card-icon blue"><i class="fas fa-child"></i></div>
        <div class="card-content"><h3>Perfil da Criança</h3><p><?php echo htmlspecialchars($nome_aluno_associado); ?></p></div>
    </a>
    <a href="atestados_responsavel.php" class="summary-card">
        <div class="card-icon red"><i class="fas fa-notes-medical"></i></div>
        <div class="card-content"><h3>Atestados</h3><p>Enviar ou ver histórico</p></div>
    </a>
    <a href="ocorrencias_responsavel.php" class="summary-card">
        <div class="card-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="card-content"><h3>Ocorrências</h3><p><?php echo $total_ocorrencias; ?> registrada(s)</p></div>
    </a>
    <a href="avisos_responsavel.php" class="summary-card">
        <div class="card-icon green"><i class="fas fa-bell"></i></div>
        <div class="card-content"><h3>Avisos</h3><p>Ver mural completo</p></div>
    </a>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-bell"></i><h3 class="section-title">Avisos Recentes</h3></div>
    <div class="card-body">
        <?php if (empty($avisos)): ?>
            <p>Nenhum aviso recente.</p>
        <?php else: ?>
            <?php foreach ($avisos as $aviso): ?>
                <div class="notice-card">
                    <h4 class="card-title"><?php echo htmlspecialchars($aviso['titulo']); ?></h4>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($aviso['conteudo'])); ?></p>
                    <div class="card-meta"><span><i class="far fa-clock"></i> <?php echo date('d/m/Y', strtotime($aviso['data_publicacao'])); ?></span></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="card-actions">
            <a href="avisos_responsavel.php" class="btn btn-secondary">Ver todos os avisos</a>
        </div>
    </div>
</div>

<?php
require_once '../templates/footer_responsavel.php';
?>