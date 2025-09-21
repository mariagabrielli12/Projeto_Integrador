<?php
$page_title = 'Registo de Rotina Diária';
$page_icon = 'fas fa-clipboard-list';
require_once 'templates/header_bercarista.php';

// --- LÓGICA DA PÁGINA ---
// Busca as turmas associadas ao berçarista logado para popular o seletor
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
    <div class="card-header">
        <h3 class="section-title"><i class="fas fa-plus-circle"></i> Novo Registo de Rotina para o Dia de Hoje</h3>
    </div>
    <div class="card-body">
        <form id="form-rotina" method="POST" action="processa_registro_diario.php">
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
                    <label for="aluno_id">Criança*</label>
                    <select id="aluno_id" name="aluno_id" required disabled>
                        <option value="">Aguardando seleção de turma...</option>
                    </select>
                </div>
            </div>
            
            <hr style="margin: 20px 0;">
            
            <h4 class="section-title" style="font-size: 1.1em;">Detalhes do Dia</h4>
            <div class="form-group">
                <label for="alimentacao">Alimentação</label>
                <textarea id="alimentacao" name="registros[Alimentação]" placeholder="Ex: Comeu bem a sopa, aceitou a fruta, bebeu 150ml de leite..." rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="sono">Sono</label>
                <textarea id="sono" name="registros[Sono]" placeholder="Ex: Dormiu por 1h30, acordou tranquilo..." rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="higiene">Higiene</label>
                <textarea id="higiene" name="registros[Higiene]" placeholder="Ex: 3 trocas de fralda, com xixi e cocó..." rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="observacoes">Observações Gerais</label>
                <textarea id="observacoes" name="registros[Observações]" placeholder="Outras informações importantes sobre o dia da criança (brincadeiras, interações, etc.)" rows="3"></textarea>
            </div>

            <div class="form-actions">
                <button class="btn btn-secondary" type="reset"><i class="fas fa-times"></i> Limpar</button>
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Registar Rotina</button>
            </div>
        </form>
    </div>
</div>

<?php
// Script JavaScript para carregar dinamicamente os alunos da turma selecionada
$extra_js = '<script>
function carregarAlunos(turmaId) {
    const alunoSelect = document.getElementById("aluno_id");
    alunoSelect.innerHTML = \'<option value="">A carregar...</option>\';
    alunoSelect.disabled = true;

    if (!turmaId) {
        alunoSelect.innerHTML = \'<option value="">Aguardando seleção de turma...</option>\';
        return;
    }

    fetch(`api_get_alunos.php?turma_id=${turmaId}`)
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
// Inclui o footer e o script JS
require_once 'templates/footer_bercarista.php';
?>