<?php
// Garante que o header e a conexão com o banco sejam incluídos
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

$page_title = 'Perfil do Aluno';
$page_icon = 'fas fa-user';
$breadcrumb = 'Portal do Professor > Perfil do Aluno';

// --- LÓGICA DO BANCO DE DADOS ATUALIZADA ---
$aluno_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($aluno_id === 0) {
    echo "<p class='alert error'>ID do aluno não fornecido.</p>";
    require_once 'templates/footer_professor.php';
    exit();
}

// Busca os dados principais do aluno
$aluno = null;
$stmt_aluno = $conexao->prepare(
    "SELECT a.nome_completo, a.data_nascimento, a.id_turma, t.nome_turma
     FROM alunos a
     LEFT JOIN turmas t ON a.id_turma = t.id_turma
     WHERE a.id_aluno = ?"
);
$stmt_aluno->bind_param("i", $aluno_id);
$stmt_aluno->execute();
$result_aluno = $stmt_aluno->get_result();
if ($result_aluno->num_rows > 0) {
    $aluno = $result_aluno->fetch_assoc();
}
$stmt_aluno->close();

if (!$aluno) {
    echo "<p class='alert error'>Aluno não encontrado.</p>";
    require_once 'templates/footer_professor.php';
    exit();
}

// Busca o histórico de ocorrências do aluno
$ocorrencias = [];
$stmt_ocorrencias = $conexao->prepare(
    "SELECT data_ocorrencia, tipo, descricao
     FROM ocorrencias
     WHERE id_aluno = ?
     ORDER BY data_ocorrencia DESC"
);
$stmt_ocorrencias->bind_param("i", $aluno_id);
$stmt_ocorrencias->execute();
$result_ocorrencias = $stmt_ocorrencias->get_result();
if ($result_ocorrencias) {
    $ocorrencias = $result_ocorrencias->fetch_all(MYSQLI_ASSOC);
}
$stmt_ocorrencias->close();
?>

<div class="card">
    <div class="card-header">
        <h3>Dossiê de <?php echo htmlspecialchars($aluno['nome_completo']); ?></h3>
    </div>
    <div class="card-body">
        <h4 class="section-title">Dados Pessoais</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Turma</label>
                <input type="text" value="<?php echo htmlspecialchars($aluno['nome_turma'] ?? 'Não matriculado'); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Nascimento</label>
                <input type="text" value="<?php echo date('d/m/Y', strtotime($aluno['data_nascimento'])); ?>" readonly>
            </div>
        </div>

        <h4 class="section-title" style="margin-top: 20px;">Histórico de Ocorrências</h4>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Data</th><th>Tipo</th><th>Descrição</th></tr></thead>
                <tbody>
                    <?php if (empty($ocorrencias)): ?>
                        <tr><td colspan="3">Nenhuma ocorrência encontrada.</td></tr>
                    <?php else: ?>
                        <?php foreach($ocorrencias as $oc): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($oc['data_ocorrencia'])); ?></td>
                            <td><?php echo htmlspecialchars($oc['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($oc['descricao']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="form-actions" style="margin-top: 20px; justify-content: flex-start;">
            <a href="detalhes_turma.php?id=<?php echo $aluno['id_turma']; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar para a Turma</a>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer_professor.php';
?>