<?php
$page_title = 'Relatórios';
$page_icon = 'fas fa-file-alt';
require_once '../templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS ---
// Busca os relatórios (observações de desenvolvimento) do aluno associado
$query = "
    SELECT 
        do.id, 
        do.data_observacao, 
        do.area_desenvolvimento,
        do.habilidade_observada,
        u.nome as nome_professor
    FROM desenvolvimento_observacoes do
    JOIN usuarios u ON do.professor_id = u.id
    WHERE do.aluno_id = ?
    ORDER BY do.data_observacao DESC
";
$stmt = $conexao->prepare($query);
$stmt->bind_param("i", $aluno_id_associado);
$stmt->execute();
$relatorios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
// --- FIM DA LÓGICA ---
?>

<div class="card">
    <div class="card-header"><i class="fas fa-chart-bar"></i><h3 class="section-title">Relatórios de Desenvolvimento</h3></div>
    <div class="card-body">
        <?php if (empty($relatorios)): ?>
            <p style="text-align: center;">Nenhum relatório de desenvolvimento disponível no momento.</p>
        <?php else: ?>
            <?php foreach ($relatorios as $relatorio): ?>
                <div class="report-card">
                    <h3 class="card-title"><i class="fas fa-child"></i> Relatório de <?php echo htmlspecialchars($relatorio['area_desenvolvimento']); ?></h3>
                    <p class="card-meta">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($relatorio['data_observacao'])); ?></span>
                        <span><i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($relatorio['nome_professor']); ?></span>
                    </p>
                    <p class="card-text">
                        Relatório sobre a habilidade: <strong><?php echo htmlspecialchars($relatorio['habilidade_observada']); ?></strong>.
                    </p>
                    <div class="card-actions">
                        <a href="detalhe_relatorio.php?id=<?php echo $relatorio['id']; ?>" class="btn"><i class="fas fa-eye"></i> Visualizar Relatório Completo</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once '../templates/footer_responsavel.php';
?>