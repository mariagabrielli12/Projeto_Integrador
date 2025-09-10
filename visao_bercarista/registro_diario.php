<?php
$page_title = 'Registo de Rotina Diária';
$page_icon = 'fas fa-clipboard-list';
require_once 'templates/header_bercarista.php';

// --- LÓGICA DA BASE DE DADOS ---
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
    <div class="card-header">
        <h3 class="section-title">Novo Registo de Rotina</h3>
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
                    <label for="aluno_id">Aluno*</label>
                    <select id="aluno_id" name="aluno_id" required disabled>
                        <option value="">Aguardando seleção de turma...</option>
                    </select>
                </div>
            </div>
            
            <h4 class="section-title" style="font-size: 1em; margin-top: 20px;">Detalhes do Dia</h4>
            <div class="form-group">
                <label>Alimentação</label>
                <textarea name="registros[Alimentação]" placeholder="Detalhes sobre as refeições (ex: comeu bem a sopa, aceitou a fruta, etc.)" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Sono</label>
                <textarea name="registros[Sono]" placeholder="Detalhes sobre o sono (ex: dormiu por 1h30, acordou tranquilo)" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Higiene</label>
                <textarea name="registros[Higiene]" placeholder="Detalhes sobre a higiene (ex: 3 trocas de fralda, xixi e cocó)" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Observações Gerais</label>
                <textarea name="registros[Observações]" placeholder="Outras informações importantes sobre o dia da criança" rows="3"></textarea>
            </div>

            <div class="form-actions">
                <button class="btn btn-secondary" type="reset"><i class="fas fa-times"></i> Limpar</button>
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Registar Atividade</button>
            </div>
        </form>
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
echo $extra_js; // Imprime o script no footer
require_once 'templates/footer_bercarista.php';
?>