<?php
$page_title = 'Avisos e Comunicados';
$page_icon = 'fas fa-bell';
require_once 'templates/header_bercarista.php';

// Busca os avisos destinados a FUNCIONARIOS ou GERAL
$avisos = $conexao->query("
    SELECT titulo, descricao, data_aviso, categoria 
    FROM avisos 
    WHERE destinatario IN ('GERAL', 'FUNCIONARIOS')
    ORDER BY data_aviso DESC
")->fetch_all(MYSQLI_ASSOC);

?>

<div class="card">
    <div class="card-header">
        <h3 class="section-title"><i class="fas fa-bullhorn"></i> Mural de Avisos da Gestão</h3>
        <br>
    </div>
    <div class="card-body">
        <?php if (empty($avisos)): ?>
            <p style="text-align: center; padding: 20px;">Nenhum aviso encontrado no momento.</p>
        <?php else: ?>
            <?php foreach ($avisos as $aviso): ?>
                <div class="notice-card" style="margin-bottom: 15px; border: 1px solid #eee; padding: 15px 20px; border-radius: 8px; background: #f9f9f9;">
                    <h4 class="card-title" style="font-size: 1.2em; color: var(--primary); margin-bottom: 5px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-info-circle" style="color: var(--primary-light);"></i> 
                        <?php echo htmlspecialchars($aviso['titulo']); ?>
                    </h4>
                    <p class="card-meta" style="font-size: 0.8em; color: #777; margin-bottom: 10px; margin-left: 28px;">
                        <span><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($aviso['data_aviso'])); ?></span> | 
                        <span><i class="fas fa-tag"></i> Categoria: <?php echo htmlspecialchars($aviso['categoria']); ?></span>
                    </p>
                    <p class="card-text" style="line-height: 1.6; margin-left: 28px;">
                        <?php echo nl2br(htmlspecialchars($aviso['descricao'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'templates/footer_bercarista.php';
?>