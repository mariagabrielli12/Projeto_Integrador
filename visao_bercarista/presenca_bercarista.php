<?php
$page_title = 'Controle de Presença';
$page_icon = 'fas fa-user-check';
require_once 'templates/header_bercarista.php';

// Busca as turmas associadas ao berçarista logado
$turmas = [];
$stmt_turmas = $conexao->prepare("SELECT id_turma, nome_turma FROM turmas WHERE id_bercarista = ? ORDER BY nome_turma");
$stmt_turmas->bind_param("i", $id_bercarista_logado);
$stmt_turmas->execute();
$result_turmas = $stmt_turmas->get_result();
if ($result_turmas) {
    $turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);
}
$stmt_turmas->close();
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Registrar Presença do Dia</h3></div>
    <div class="card-body">
        <form id="form-presenca" method="POST" action="processa_presenca.php">
            <div class="form-row" style="align-items: flex-end;">
                <div class="form-group">
                    <label for="data">Data da Presença:</label>
                    <input type="date" id="data" name="data" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="turma_id">Selecionar Turma:</label>
                    <select id="turma_id" name="turma_id" class="form-control" onchange="carregarAlunosPresenca(this.value)" required>
                        <option value="">Selecione uma Turma</option>
                        <?php foreach($turmas as $turma): ?>
                            <option value="<?php echo $turma['id_turma']; ?>"><?php echo htmlspecialchars($turma['nome_turma']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table">
                    <thead>
                        <tr><th>Nome da Criança</th><th>Status de Presença</th></tr>
                    </thead>
                    <tbody id="attendance-table-body">
                        <tr><td colspan="2" style="text-align: center;">Selecione uma turma para carregar os alunos.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="form-actions" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Presença</button>
            </div>
        </form>
    </div>
</div>

<?php
$extra_js = '<script>
function carregarAlunosPresenca(turmaId) {
    const tbody = document.getElementById("attendance-table-body");
    tbody.innerHTML = \'<tr><td colspan="2" style="text-align:center;">Carregando...</td></tr>\';

    if (!turmaId) {
        tbody.innerHTML = \'<tr><td colspan="2" style="text-align: center;">Selecione uma turma para carregar os alunos.</td></tr>\';
        return;
    }

    fetch(`api_get_alunos.php?turma_id=${turmaId}`)
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = ""; // Limpa a tabela
            if (data.length > 0) {
                data.forEach(aluno => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${aluno.nome_completo}</td>
                        <td>
                            <div class="radio-group">
                                <label><input type="radio" name="status[${aluno.id_aluno}]" value="presente" checked> Presente</label>
                                <label><input type="radio" name="status[${aluno.id_aluno}]" value="ausente"> Ausente</label>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = \'<tr><td colspan="2" style="text-align: center;">Nenhuma criança encontrada para esta turma.</td></tr>\';
            }
        });
}
</script>';
echo $extra_js;
require_once 'templates/footer_bercarista.php';
?>