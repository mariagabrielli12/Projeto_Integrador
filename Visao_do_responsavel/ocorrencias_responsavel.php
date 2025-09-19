<?php
$page_title = 'Ocorrências';
$page_icon = 'fas fa-exclamation-triangle';
require_once 'templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS (ATUALIZADO) ---

// 1. Busca o ID do aluno associado a este responsável
$aluno_info = [];
$stmt_aluno = $conexao->prepare("SELECT a.id_aluno, a.nome_completo FROM alunos a JOIN alunos_responsaveis ar ON a.id_aluno = ar.id_aluno WHERE ar.id_responsavel = ? LIMIT 1");
$stmt_aluno->bind_param("i", $id_responsavel_logado);
$stmt_aluno->execute();
$result_aluno = $stmt_aluno->get_result();
if ($result_aluno->num_rows > 0) {
    $aluno_info = $result_aluno->fetch_assoc();
}
$stmt_aluno->close();

// 2. Busca as ocorrências do aluno
$ocorrencias = [];
if (!empty($aluno_info)) {
    $stmt_ocorrencias = $conexao->prepare(
        "SELECT o.*, u.nome_completo AS nome_registrou
         FROM ocorrencias o
         LEFT JOIN usuarios u ON o.id_registrado_por = u.id_usuario
         WHERE o.id_aluno = ?
         ORDER BY o.data_ocorrencia DESC"
    );
    $stmt_ocorrencias->bind_param("i", $aluno_info['id_aluno']);
    $stmt_ocorrencias->execute();
    $ocorrencias = $stmt_ocorrencias->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_ocorrencias->close();
}
?>

<div class="card">
    <div class="card-header"><i class="fas fa-history"></i><h3 class="section-title">Histórico de Ocorrências de <?php echo htmlspecialchars($aluno_info['nome_completo'] ?? 'seu/sua filho(a)'); ?></h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Data</th><th>Tipo</th><th>Observação</th><th>Registado por</th></tr></thead>
                <tbody>
                    <?php if (empty($ocorrencias)): ?>
                        <tr><td colspan="4" style="text-align: center;">Nenhuma ocorrência registada.</td></tr>
                    <?php else: ?>
                        <?php foreach ($ocorrencias as $ocorrencia): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($ocorrencia['data_ocorrencia'])); ?></td>
                            <td><?php echo htmlspecialchars($ocorrencia['tipo']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($ocorrencia['descricao'])); ?></td>
                            <td><?php echo htmlspecialchars($ocorrencia['nome_registrou'] ?? 'Sistema'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer_responsavel.php';
?>