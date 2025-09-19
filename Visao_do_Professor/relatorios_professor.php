<?php
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

$page_title = 'Relatórios';
$page_icon = 'fas fa-file-alt';
$breadcrumb = 'Portal do Professor > Acompanhamento > Relatórios';

// Busca as turmas do professor para o formulário
$turmas = [];
$stmt_turmas = $conexao->prepare("SELECT id_turma, nome_turma FROM turmas WHERE id_professor = ? ORDER BY nome_turma");
$stmt_turmas->bind_param("i", $id_professor_logado);
$stmt_turmas->execute();
$result_turmas = $stmt_turmas->get_result();
if($result_turmas) {
    $turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);
}
$stmt_turmas->close();
?>

<div class="welcome-banner">
  <h3>Geração de Relatórios</h3>
  <p>Exporte relatórios sobre o desenvolvimento e atividades das crianças.</p>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card">
      <h4><i class="fas fa-child"></i> Relatório Individual de Desenvolvimento</h4>
      <form method="POST" action="gerar_relatorio_individual.php" target="_blank">
          <div class="form-group" style="margin-top: 15px;">
            <label for="turma_id_relatorio">Selecione a turma:</label>
            <select id="turma_id_relatorio" name="turma_id" onchange="carregarAlunos(this.value, 'aluno_id_relatorio')" required>
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?php echo $turma['id_turma']; ?>"><?php echo htmlspecialchars($turma['nome_turma']); ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin-top: 15px;">
            <label for="aluno_id_relatorio">Selecione a criança:</label>
            <select id="aluno_id_relatorio" name="aluno_id" required disabled>
                <option value="">Aguardando turma...</option>
            </select>
          </div>
          <div class="form-group">
            <label>Período:</label>
            <div style="display: flex; gap: 10px;">
              <input type="date" name="data_inicio" style="flex: 1;" required>
              <span>até</span>
              <input type="date" name="data_fim" style="flex: 1;" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
            <i class="fas fa-download"></i> Gerar Relatório
          </button>
      </form>
    </div>
</div>

<?php
// Adiciona o JavaScript para carregar os alunos dinamicamente
$extra_js = '<script>
function carregarAlunos(turmaId, selectId) {
    const alunoSelect = document.getElementById(selectId);
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
require_once 'templates/footer_professor.php';
?>