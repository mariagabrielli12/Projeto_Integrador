<?php
$page_title = 'Ocorrências';
$page_icon = 'fas fa-exclamation-triangle';
require_once 'templates/header_bercarista.php';

// --- LÓGICA DA PÁGINA ---
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

// Busca as ocorrências já registadas nas turmas deste berçarista
$ocorrencias = [];
$sql_ocorrencias = "
    SELECT o.data_ocorrencia, o.tipo, o.descricao, a.nome_completo as nome_aluno, t.nome_turma
    FROM ocorrencias o
    JOIN alunos a ON o.id_aluno = a.id_aluno
    JOIN turmas t ON a.id_turma = t.id_turma
    WHERE t.id_bercarista = ?
    ORDER BY o.data_ocorrencia DESC
";
$stmt_ocorrencias = $conexao->prepare($sql_ocorrencias);
$stmt_ocorrencias->bind_param("i", $id_bercarista_logado);
$stmt_ocorrencias->execute();
$result_ocorrencias = $stmt_ocorrencias->get_result();
if($result_ocorrencias) {
    $ocorrencias = $result_ocorrencias->fetch_all(MYSQLI_ASSOC);
}
$stmt_ocorrencias->close();
?>



    <div id="nova-ocorrencia" class="tab-content active">
        <div class="card-body">
            <form id="form-ocorrencia" method="POST" action="processa_ocorrencia_bercarista.php">
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
                            <option value="">Selecione a Turma Primeiro</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo">Tipo de Ocorrência*</label>
                        <select id="tipo" name="tipo" required>
                            <option value="Saúde">Saúde</option>
                            <option value="Comportamento">Comportamento</option>
                            <option value="Acidente">Acidente</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="data_ocorrencia">Data e Hora*</label>
                        <input type="datetime-local" id="data_ocorrencia" name="data_ocorrencia" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição Detalhada*</label>
                    <textarea id="descricao" name="descricao" rows="5" placeholder="Descreva o que aconteceu de forma clara e objetiva..." required></textarea>
                </div>
                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary"><i class="fas fa-times"></i> Limpar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar Ocorrência</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="historico" class="tab-content">
        <div class="card-body">
            <div class="table-responsive">
              <table class="table">
                <thead><tr><th>Data</th><th>Turma</th><th>Aluno</th><th>Tipo</th><th>Descrição</th></tr></thead>
                <tbody>
                    <?php if (empty($ocorrencias)): ?>
                        <tr><td colspan="5">Nenhuma ocorrência registrada para as suas turmas.</td></tr>
                    <?php else: ?>
                        <?php foreach($ocorrencias as $oc): ?>
                          <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($oc['data_ocorrencia'])); ?></td>
                            <td><?php echo htmlspecialchars($oc['nome_turma']); ?></td>
                            <td><?php echo htmlspecialchars($oc['nome_aluno']); ?></td>
                            <td><?php echo htmlspecialchars($oc['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($oc['descricao']); ?></td>
                          </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
              </table>
            </div>
        </div>
    </div>
</div>

<?php
$extra_js = '<script>
    function openTab(evt, tabName) {
      let i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tab-content");
      for (i = 0; i < tabcontent.length; i++) { tabcontent[i].classList.remove("active"); }
      tablinks = document.getElementsByClassName("tab-btn");
      for (i = 0; i < tablinks.length; i++) { tablinks[i].classList.remove("active"); }
      document.getElementById(tabName).classList.add("active");
      evt.currentTarget.classList.add("active");
    }
    
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

    document.addEventListener("DOMContentLoaded", function() {
        // Garante que a primeira aba esteja ativa ao carregar
        document.querySelector(".tab-btn.active").click();
    });
</script>';
echo $extra_js;
require_once 'templates/footer_bercarista.php';
?>