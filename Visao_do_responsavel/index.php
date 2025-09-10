<?php
$page_title = 'Página Inicial';
$page_icon = 'fas fa-home';
require_once 'templates/header_responsavel.php';

// --- BUSCA DE DADOS DINÂMICOS (ATUALIZADO) ---

// 1. Encontra as informações do aluno associado a este responsável
$aluno_info = [];
$stmt_aluno = $conexao->prepare(
    "SELECT a.id_aluno, a.nome_completo 
     FROM alunos a
     JOIN alunos_responsaveis ar ON a.id_aluno = ar.id_aluno
     WHERE ar.id_responsavel = ? 
     LIMIT 1"
);
$stmt_aluno->bind_param("i", $id_responsavel_logado);
$stmt_aluno->execute();
$result_aluno = $stmt_aluno->get_result();
if ($result_aluno->num_rows > 0) {
    $aluno_info = $result_aluno->fetch_assoc();
}
$stmt_aluno->close();

// 2. Busca o número de ocorrências do aluno
$ocorrencias_count = 0;
if (!empty($aluno_info)) {
    $stmt_ocorrencias = $conexao->prepare("SELECT COUNT(*) as total FROM ocorrencias WHERE id_aluno = ?");
    $stmt_ocorrencias->bind_param("i", $aluno_info['id_aluno']);
    $stmt_ocorrencias->execute();
    $ocorrencias_count = $stmt_ocorrencias->get_result()->fetch_assoc()['total'];
    $stmt_ocorrencias->close();
}

// 3. Busca os 2 avisos mais recentes
$avisos_recentes = $conexao->query("SELECT titulo, descricao, data_aviso FROM avisos ORDER BY data_aviso DESC LIMIT 2");
?>
<div class="summary-cards">
    <a href="perfil_crianca.php" class="summary-card">
        <div class="card-icon blue"><i class="fas fa-child"></i></div>
        <div class="card-content">
            <h3>Perfil da Criança</h3>
            <p><?php echo htmlspecialchars($aluno_info['nome_completo'] ?? 'Nenhuma criança associada'); ?></p>
        </div>
    </a>
    <a href="atestados_responsavel.php" class="summary-card">
        <div class="card-icon red"><i class="fas fa-notes-medical"></i></div>
        <div class="card-content"><h3>Atestados</h3><p>Enviar ou visualizar</p></div>
    </a>
    <a href="ocorrencias_responsavel.php" class="summary-card">
        <div class="card-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="card-content"><h3>Ocorrências</h3><p><?php echo $ocorrencias_count; ?> registo(s)</p></div>
    </a>
    <a href="avisos_responsavel.php" class="summary-card">
        <div class="card-icon green"><i class="fas fa-bell"></i></div>
        <div class="card-content"><h3>Avisos</h3><p>Ver comunicados</p></div>
    </a>
</div>
<div class="card">
    <div class="card-header"><i class="fas fa-bell"></i><h3 class="section-title">Avisos Recentes</h3></div>
    <div class="card-body">
        <?php if ($avisos_recentes && $avisos_recentes->num_rows > 0): ?>
            <?php while($aviso = $avisos_recentes->fetch_assoc()): ?>
                <div class="notice-card">
                    <h4 class="card-title"><?php echo htmlspecialchars($aviso['titulo']); ?></h4>
                    <p class="card-text"><?php echo htmlspecialchars($aviso['descricao']); ?></p>
                    <div class="card-meta"><span><i class="far fa-clock"></i> <?php echo date("d/m/Y", strtotime($aviso['data_aviso'])); ?></span></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum aviso recente.</p>
        <?php endif; ?>
        <div class="card-actions"><a href="avisos_responsavel.php" class="btn btn-secondary">Ver todos os avisos</a></div>
    </div>
</div>

<?php require_once 'templates/footer_responsavel.php'; ?>