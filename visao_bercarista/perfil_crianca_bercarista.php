<?php
$page_title = 'Perfil da Criança';
$page_icon = 'fas fa-child';
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
    <div class="card-header"><h3 class="section-title">Selecionar Criança</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label for="select-turma">Turma</label>
                <select id="select-turma" class="form-control" onchange="carregarAlunosParaPerfil(this.value)">
                    <option value="">Selecione uma turma</option>
                    <?php foreach($turmas as $turma): ?>
                        <option value="<?php echo $turma['id_turma']; ?>"><?php echo htmlspecialchars($turma['nome_turma']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="select-aluno">Criança</label>
                <select id="select-aluno" class="form-control" disabled onchange="verPerfil()">
                    <option value="">Aguardando seleção de turma...</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card" id="perfil-info-card" style="margin-top: 25px; display: none;">
    <div class="card-header"><h3 class="section-title">Informações da Criança</h3></div>
    <div class="card-body" id="perfil-body">
        <p class="text-center">Aguardando seleção de um aluno...</p>
    </div>
</div>

<?php
$extra_js = '<script>
    function carregarAlunosParaPerfil(turmaId) {
        const alunoSelect = document.getElementById("select-aluno");
        const perfilCard = document.getElementById("perfil-info-card");
        perfilCard.style.display = "none";
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
    
    function verPerfil() {
        const alunoId = document.getElementById("select-aluno").value;
        const perfilCard = document.getElementById("perfil-info-card");
        const perfilBody = document.getElementById("perfil-body");

        if (!alunoId) {
            perfilCard.style.display = "none";
            return;
        }

        perfilBody.innerHTML = "<p>A carregar perfil...</p>";
        perfilCard.style.display = "block";

        fetch(`api_get_perfil_aluno.php?id=${alunoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    perfilBody.innerHTML = `<p class="alert error">${data.error}</p>`;
                } else {
                    perfilBody.innerHTML = `
                        <div class="form-row">
                            <div class="form-group"><label>Nome Completo</label><input type="text" value="${data.nome_completo}" readonly></div>
                            <div class="form-group"><label>Data de Nascimento</label><input type="text" value="${data.data_nascimento}" readonly></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Turma</label><input type="text" value="${data.nome_turma}" readonly></div>
                        </div>
                        <h4 class="section-title" style="font-size: 1.1em; margin-top: 20px;">Contato de Emergência</h4>
                        <div class="form-row">
                            <div class="form-group"><label>Nome do Responsável</label><input type="text" value="${data.responsavel_nome}" readonly></div>
                            <div class="form-group"><label>Telefone</label><input type="text" value="${data.responsavel_contato}" readonly></div>
                        </div>
                    `;
                }
            });
    }
</script>';
echo $extra_js;
require_once 'templates/footer_bercarista.php';
?>