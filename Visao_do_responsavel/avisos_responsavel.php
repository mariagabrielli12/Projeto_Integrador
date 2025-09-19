<?php
$page_title = 'Avisos';
$page_icon = 'fas fa-bell';
require_once 'templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS (ATUALIZADO) ---
$query = "
    SELECT a.titulo, a.descricao, a.data_aviso, u.nome_completo as remetente
    FROM avisos a
    LEFT JOIN usuarios u ON a.id_secretario = u.id_usuario
    WHERE a.destinatario = 'GERAL'
    ORDER BY a.data_aviso DESC
";
$resultado = $conexao->query($query);
$avisos = [];
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $avisos[] = $row;
    }
}
?>

<div class="card">
    <div class="card-header"><i class="fas fa-clipboard-list"></i><h3 class="section-title">Mural de Avisos</h3></div>
    <div class="card-body">
        <?php if (empty($avisos)): ?>
            <p style="text-align: center;">Não há avisos no momento.</p>
        <?php else: ?>
            <?php foreach ($avisos as $aviso): ?>
                <div class="notice-card">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i><?php echo htmlspecialchars($aviso['titulo']); ?></h3>
                    <p class="card-meta">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($aviso['data_aviso'])); ?></span>
                        <span><i class="fas fa-user"></i> Remetente: <?php echo htmlspecialchars($aviso['remetente'] ?? 'Secretaria'); ?></span>
                    </p>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($aviso['descricao'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'templates/footer_responsavel.php';
?>