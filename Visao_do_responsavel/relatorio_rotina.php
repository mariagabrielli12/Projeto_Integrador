<?php
require_once 'templates/header_responsavel.php';
$page_title = 'Relatório da Rotina Diária';
$page_icon = 'fas fa-clipboard-list';

// Define a data a ser consultada (hoje por padrão, ou a data do GET)
$data_selecionada = $_GET['data'] ?? date('Y-m-d');

// Busca os registros diários para o aluno e a data selecionada
$registros = [];
if ($id_aluno_logado > 0) {
    $sql = "
        SELECT rd.*, u.nome_completo as nome_bercarista
        FROM registros_diarios rd
        LEFT JOIN usuarios u ON rd.id_professor = u.id_usuario
        WHERE rd.id_aluno = ? AND rd.data = ?
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("is", $id_aluno_logado, $data_selecionada);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $registros[$row['tipo_registro']] = $row;
    }
}
?>

<div class="card">
    <div class="card-header"><h3 class="section-title"><i class="fas fa-clipboard-list"></i> Relatório da Rotina Diária</h3></div>
    <div class="card-body">
        <form method="GET" action="" class="mb-4">
            <div class="form-row align-items-end">
                <div class="form-group">
                    <label for="data" class="form-label"><strong>Selecione o dia:</strong></label>
                    <input type="date" id="data" name="data" class="form-control" value="<?php echo htmlspecialchars($data_selecionada); ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100">Consultar</button>
                </div>
            </div>
        </form>

        <?php if (empty($registros)): ?>
            <div class="alert alert-info">Nenhum registro de rotina encontrado para esta data.</div>
        <?php else: ?>
            <div class="rotina-card">
                <h4><i class="fas fa-utensils"></i> Alimentação</h4>
                <p><?php echo htmlspecialchars($registros['Alimentação']['descricao'] ?? 'Nenhum registro de alimentação.'); ?></p>
            </div>
            <div class="rotina-card">
                <h4><i class="fas fa-bed"></i> Sono</h4>
                 <p><?php echo htmlspecialchars($registros['Sono']['descricao'] ?? 'Nenhum registro de sono.'); ?></p>
            </div>
            <div class="rotina-card">
                <h4><i class="fas fa-bath"></i> Higiene</h4>
                 <p><?php echo htmlspecialchars($registros['Higiene']['descricao'] ?? 'Nenhum registro de higiene.'); ?></p>
            </div>
             <div class="rotina-card">
                <h4><i class="fas fa-comment-alt"></i> Observações Gerais</h4>
                <p><?php echo htmlspecialchars($registros['Observações']['descricao'] ?? 'Nenhuma observação geral.'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'templates/footer_responsavel.php';
?>