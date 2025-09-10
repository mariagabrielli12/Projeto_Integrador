<?php
// Garante que o header e a conexão com o banco sejam incluídos
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

// Define as variáveis da página
$page_title = 'Registrar Observação';
$page_icon = 'fas fa-edit';
$breadcrumb = 'Portal do Professor > Acompanhamento > Registrar Observação';

// Pega o ID do professor da sessão
$id_professor_logado = $_SESSION['id'] ?? 1;

// Busca as turmas do professor para o formulário
$turmas = [];
$stmt_turmas = $conexao->prepare("SELECT ID_Turma, Nome_Turma FROM turmas WHERE prof_id = ? ORDER BY Nome_Turma");
$stmt_turmas->bind_param("i", $id_professor_logado);
$stmt_turmas->execute();
$result_turmas = $stmt_turmas->get_result();
if($result_turmas) {
    $turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);
}
$stmt_turmas->close();
?>

<div class="form-container">
    <form method="POST" action="processa_observacao.php">
        <div class="form-row">
             <div class="form-group">
                <label for="turma_id">Turma*</label>
                <select id="turma_id" name="turma_id" onchange="carregarAlunos(this.value)" required>
                    <option value="">Selecione a turma</option>
                    <?php foreach($turmas as $turma): ?>
                        <option value="<?php echo $turma['ID_Turma']; ?>"><?php echo htmlspecialchars($turma['Nome_Turma']); ?></option>
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
                <label for="data">Data da Observação*</label>
                <input type="date" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label for="area">Área de Desenvolvimento*</label>
                <select id="area" name="area" required>
                    <option value="">Selecione...</option>
                    <option value="Motor">Motor</option>
                    <option value="Cognitivo">Cognitivo</option>
                    <option value="Socioafetivo">Socioafetivo</option>
                    <option value="Linguagem">Linguagem</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="habilidade">Habilidade Específica Observada*</label>
            <input type="text" id="habilidade" name="habilidade" placeholder="Ex: Equilíbrio, vocabulário, interação social..." required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição da Observação*</label>
            <textarea id="descricao" name="descricao" rows="4" placeholder="Descreva o comportamento ou ação observada detalhadamente..." required></textarea>
        </div>
        <div class="form-actions">
            <a href="desenvolvimento_aluno.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Observação</button>
        </div>
    </form>
</div>

<?php
// Adiciona o JavaScript para carregar os alunos dinamicamente
$extra_js = '<script>
function carregarAlunos(turmaId) {
    const alunoSelect = document.getElementById("aluno_id");
    alunoSelect.innerHTML = \'<option value="">Carregando...</option>\';
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
                    option.value = aluno.ID_Aluno;
                    option.textContent = aluno.Nome;
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