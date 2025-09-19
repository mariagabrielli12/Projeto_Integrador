<?php
$page_title = 'Início - Berçarista';
$page_icon = 'fas fa-home';
require_once 'templates/header_bercarista.php';

// --- LÓGICA DA BASE DE DADOS ---
// Conta as ocorrências pendentes nas turmas do berçarista
$ocorrencias_count = 0;
$stmt_ocorrencias = $conexao->prepare(
    "SELECT COUNT(o.id_ocorrencia) as total 
     FROM ocorrencias o
     JOIN alunos a ON o.id_aluno = a.id_aluno
     JOIN turmas t ON a.id_turma = t.id_turma
     WHERE t.id_bercarista = ? AND o.status = 'Pendente'"
);
$stmt_ocorrencias->bind_param("i", $id_bercarista_logado);
$stmt_ocorrencias->execute();
$result = $stmt_ocorrencias->get_result();
if ($result) {
    $ocorrencias_count = $result->fetch_assoc()['total'];
}
$stmt_ocorrencias->close();
$avisos_recentes = $conexao->query(
    "SELECT titulo, descricao, data_aviso FROM avisos 
     WHERE destinatario IN ('GERAL', 'FUNCIONARIOS') 
     ORDER BY data_aviso DESC LIMIT 2"
);
?>

<div class="summary-cards">
    <a href="registro_diario.php" class="summary-card">
      <div class="card-icon blue"><i class="fas fa-clipboard-list"></i></div>
      <div class="card-content"><h3>Registos do Dia</h3><p>Anotar alimentação, sono, etc.</p></div>
    </a>
    <a href="ocorrencias_bercarista.php" class="summary-card">
      <div class="card-icon red"><i class="fas fa-exclamation-triangle"></i></div>
      <div class="card-content"><h3>Ocorrências</h3><p><?php echo $ocorrencias_count; ?> pendente(s) para verificar</p></div>
    </a>
    <a href="avisos_bercarista.php" class="summary-card">
      <div class="card-icon green"><i class="fas fa-bell"></i></div>
      <div class="card-content"><h3>Avisos e Comunicados</h3><p>Ver o mural da secretaria</p></div>
    </a>
</div>

<div class="card">
    <div class="card-header"><h3 class="section-title">Avisos Recentes</h3></div>
    <div class="card-body">
        <?php if ($avisos_recentes && $avisos_recentes->num_rows > 0): ?>
            <?php while($aviso = $avisos_recentes->fetch_assoc()): ?>
                <div class="dashboard-notice-card" style="padding: 15px; border-bottom: 1px solid #eee;">
                    <div class="notice-title" style="font-weight: bold; display: flex; align-items: center; gap: 8px;"><i class="fas fa-info-circle"></i><span><?php echo htmlspecialchars($aviso['titulo']); ?></span></div>
                    <p class="notice-text" style="margin-left: 25px;"><?php echo htmlspecialchars($aviso['descricao']); ?></p>
                    <div class="notice-meta" style="font-size: 12px; color: #777; margin-left: 25px;"><span><i class="far fa-clock"></i> <?php echo date("d/m/Y", strtotime($aviso['data_aviso'])); ?></span></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum aviso recente.</p>
        <?php endif; ?>
        <div class="card-actions" style="margin-top:15px; text-align: right;"><a href="avisos_bercarista.php" class="btn btn-primary">Ver Todos os Avisos</a></div>
    </div>
</div>

<?php
require_once 'templates/footer_bercarista.php';
?>