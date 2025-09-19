<?php
// Garante que o header e a conexão com o banco sejam incluídos
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

// Define as variáveis da página
$page_title = 'Ocorrências';
$page_icon = 'fas fa-exclamation-triangle';
$breadcrumb = 'Portal do Professor > Ocorrências';

// --- LÓGICA DO BANCO DE DADOS ATUALIZADA ---

// 1. Busca as turmas do professor para o formulário
$turmas = [];
$stmt_turmas = $conexao->prepare("SELECT id_turma, nome_turma FROM turmas WHERE id_professor = ? ORDER BY nome_turma");
$stmt_turmas->bind_param("i", $id_professor_logado);
$stmt_turmas->execute();
$result_turmas = $stmt_turmas->get_result();
if($result_turmas) {
    $turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);
}
$stmt_turmas->close();

// 2. Busca as ocorrências já registadas por este professor
$ocorrencias = [];
$sql_ocorrencias = "
    SELECT o.data_ocorrencia, o.tipo, o.descricao, a.nome_completo as nome_aluno
    FROM ocorrencias o
    JOIN alunos a ON o.id_aluno = a.id_aluno
    WHERE o.id_registrado_por = ?
    ORDER BY o.data_ocorrencia DESC
";
$stmt_ocorrencias = $conexao->prepare($sql_ocorrencias);
$stmt_ocorrencias->bind_param("i", $id_professor_logado);
$stmt_ocorrencias->execute();
$result_ocorrencias = $stmt_ocorrencias->get_result();
if($result_ocorrencias) {
    $ocorrencias = $result_ocorrencias->fetch_all(MYSQLI_ASSOC);
}
$stmt_ocorrencias->close();
// --- FIM DA LÓGICA ---
?>

<div class="welcome-banner">
  <h3>Registo de Ocorrências</h3>
  <p>Registe eventos relevantes que necessitem de atenção especial.</p>
</div>

<div class="form-container">
    <h4><i class="fas fa-plus-circle"></i> Nova Ocorrência</h4>
    <form method="POST" action="processa_ocorrencia.php">
        <div class="form-row">
            <div class="form-group">
                <label for="turma_id">Turma*</label>
                <select id="turma_id" name="turma_id" onchange="carregarAlunos(this.value)" required>
                    <option value="">Selecione a turma</option>
                    <?php foreach($turmas as $turma): ?>
                        <option value="<?php echo $turma['id_turma']; ?>"><?php echo htmlspecialchars($turma['nome_turma']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="aluno_id">Criança*</label>
                <select id="aluno_id" name="aluno_id" required disabled>
                    <option value="">Aguardando seleção de turma...</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="data_ocorrencia">Data e Hora*</label>
                <input type="datetime-local" id="data_ocorrencia" name="data_ocorrencia" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo*</label>
                <select id="tipo" name="tipo" required>
                    <option value="Comportamento">Comportamento</option>
                    <option value="Saúde">Saúde</option>
                    <option value="Acidente">Acidente</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição*</label>
            <textarea id="descricao" name="descricao" rows="4" placeholder="Descreva detalhadamente a ocorrência" required></textarea>
        </div>
        <div class="form-actions">
            <button class="btn btn-secondary" type="reset">Cancelar</button>
            <button class="btn btn-primary" type="submit">Registar Ocorrência</button>
        </div>
    </form>
</div>

<div class="table-container" style="margin-top: 30px;">
    <h4><i class="fas fa-history"></i> Ocorrências Registadas</h4>
    <table class="table">
      <thead>
        <tr><th>Data</th><th>Criança</th><th>Tipo</th><th>Descrição</th><th>Ações</th></tr>
      </thead>
      <tbody>
        <?php if (empty($ocorrencias)): ?>
            <tr><td colspan="5">Nenhuma ocorrência registada por si.</td></tr>
        <?php else: ?>
            <?php foreach ($ocorrencias as $oc): ?>
            <tr>
              <td><?php echo date('d/m/Y H:i', strtotime($oc['data_ocorrencia'])); ?></td>
              <td><?php echo htmlspecialchars($oc['nome_aluno']); ?></td>
              <td><?php echo htmlspecialchars($oc['tipo']); ?></td>
              <td><?php echo htmlspecialchars($oc['descricao']); ?></td>
              <td>
                <button class="btn-icon" title="Editar"><i class="fas fa-edit"></i></button>
              </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
</div>

<?php
// Adiciona o JavaScript para carregar os alunos dinamicamente
$extra_js = '<script>
function carregarAlunos(turmaId) {
    const alunoSelect = document.getElementById("aluno_id");
    alunoSelect.innerHTML = \'<option value="">A carregar...</option>\';
    alunoSelect.disabled = true;

    if (!turmaId) {
        alunoSelect.innerHTML = \'<option value="">Aguardando seleção de turma...</option>\';
        return;
    }

    // Usaremos o api_get_alunos.php que já existe
    fetch("api_get_alunos.php?turma_id=" + turmaId)
        .then(response => response.json())
        .then(data => {
            alunoSelect.innerHTML = \'<option value="">Selecione uma criança</option>\';
            if (data.length > 0) {
                data.forEach(aluno => {
                    const option = document.createElement("option");
                    option.value = aluno.id_aluno; // Corrigido para id_aluno
                    option.textContent = aluno.nome_completo; // Corrigido para nome_completo
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