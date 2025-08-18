<?php
$page_title = 'Ocorrências';
$page_icon = 'fas fa-exclamation-triangle';
require_once '../templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS ---
$stmt = $conexao->prepare(
    "SELECT o.*, u.nome AS nome_registrou
     FROM ocorrencias o
     JOIN usuarios u ON o.registrado_por_id = u.id
     WHERE o.aluno_id = ?
     ORDER BY o.data_ocorrencia DESC"
);
$stmt->bind_param("i", $aluno_id_associado);
$stmt->execute();
$ocorrencias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
// --- FIM DA LÓGICA ---
?>

<div class="card">
    <div class="card-header"><i class="fas fa-history"></i><h3 class="section-title">Histórico de Ocorrências de <?php echo htmlspecialchars($nome_aluno_associado); ?></h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Data</th><th>Tipo</th><th>Observação</th><th>Registrado por</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($ocorrencias)): ?>
                        <tr><td colspan="4" style="text-align: center;">Nenhuma ocorrência registrada.</td></tr>
                    <?php else: ?>
                        <?php foreach ($ocorrencias as $ocorrencia): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($ocorrencia['data_ocorrencia'])); ?></td>
                            <td><?php echo htmlspecialchars($ocorrencia['tipo']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($ocorrencia['descricao'])); ?></td>
                            <td><?php echo htmlspecialchars($ocorrencia['nome_registrou']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../templates/footer_responsavel.php';
?>