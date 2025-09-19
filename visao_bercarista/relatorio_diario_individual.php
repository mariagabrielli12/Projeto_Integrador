<?php
require_once 'templates/header_bercarista.php';

// --- LÓGICA DA BASE DE DADOS ---
$turmas = [];
$stmt_turmas = $conexao->prepare("SELECT id_turma, nome_turma FROM turmas WHERE id_bercarista = ? ORDER BY nome_turma");
$stmt_turmas->bind_param("i", $id_bercarista_logado);
$stmt_turmas->execute();
$result_turmas = $stmt_turmas->get_result();
if ($result_turmas) {
    $turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);
}
$stmt_turmas->close();

$relatorio = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aluno_id = $_POST['aluno_id'];
    $data = $_POST['data'];

    $sql = "
        SELECT rd.tipo_registro, rd.descricao, a.nome_completo AS nome_aluno
        FROM registros_diarios rd
        JOIN alunos a ON rd.id_aluno = a.id_aluno
        WHERE rd.id_aluno = ? AND rd.data = ?
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("is", $aluno_id, $data);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $relatorio = [];
        while($row = $result->fetch_assoc()) {
            $relatorio['nome_aluno'] = $row['nome_aluno'];
            $relatorio['registros'][$row['tipo_registro']] = $row['descricao'];
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="section-title">Relatório Diário Individual</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="turma_id">Turma*</label>
                    <select id="turma_id" name="turma_id" onchange="carregarAlunos(this.value)" required>
                        <option value="">Selecione a Turma</option>
                        <?php foreach($turmas as $turma): ?>
                            <option value="<?php echo $turma['id_turma']; ?>"><?php echo htmlspecialchars($turma['nome_turma']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="aluno_id">Aluno*</label>
                    <select id="aluno_id" name="aluno_id" required disabled>
                        <option value="">Aguardando seleção de turma...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data">Data*</label>
                    <input type="date" name="data" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            </div>
        </form>

        <?php if ($relatorio): ?>
            <div id="relatorio-imprimir" style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 20px;">
                <h4>Relatório de <?php echo htmlspecialchars($relatorio['nome_aluno']); ?> - <?php echo date('d/m/Y', strtotime($_POST['data'])); ?></h4>
                <ul>
                    <?php foreach ($relatorio['registros'] as $tipo => $descricao): ?>
                        <li><strong><?php echo htmlspecialchars($tipo); ?>:</strong> <?php echo htmlspecialchars($descricao); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button onclick="window.print()" class="btn btn-secondary">Imprimir</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$extra_js = '<script>
function carregarAlunos(turmaId) {
    const alunoSelect = document.getElementById("aluno_id");
    alunoSelect.innerHTML = \'<option value="">A carregar...</option>\';
    alunoSelect.disabled = true;

    if (!turmaId) {
        alunoSelect.innerHTML = \'<option value="">Aguardando seleção de turma...</option>\';
        return;
    }

    fetch("api_get_alunos.php?turma_id=" + turmaId)
        .then(response => response.json())
        .then(data => {
            alunoSelect.innerHTML = \'<option value="">Selecione uma criança</option>\';
            if (data.length > 0) {
                data.forEach(aluno => {
                    const option = document.createElement("option");
                    option.value = aluno.id_aluno;
                    option.textContent = aluno.nome_completo;
                    alunoSelect.appendChild(option);
                });
                alunoSelect.disabled = false;
            } else {
                alunoSelect.innerHTML = \'<option value="">Nenhuma criança nesta turma</option>\';
            }
        });
}
</script>';
echo $extra_js;
require_once 'templates/footer_bercarista.php';
?>