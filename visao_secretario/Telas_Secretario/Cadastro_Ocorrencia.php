<?php
// Define a constante PROJECT_ROOT e inclui o cabeçalho
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(dirname(__DIR__)));
}
$page_title = 'Registar Ocorrência';
$page_icon = 'fas fa-exclamation-triangle';
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// Inicializa variáveis
$ocorrencia = ['id_ocorrencia' => null, 'id_aluno' => '', 'data_ocorrencia' => date('Y-m-d\TH:i'), 'tipo' => '', 'descricao' => ''];
$turma_do_aluno_selecionado = null;
$is_edit_mode = false;

// --- MODO EDIÇÃO ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_edit_mode = true;
    $page_title = 'Editar Ocorrência';
    $id_ocorrencia = $_GET['id'];
    
    // Busca a ocorrência e a turma do aluno associado
    $stmt = $conexao->prepare(
        "SELECT o.*, a.id_turma 
         FROM ocorrencias o
         JOIN alunos a ON o.id_aluno = a.id_aluno
         WHERE o.id_ocorrencia = ?"
    );
    $stmt->bind_param("i", $id_ocorrencia);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $ocorrencia = $result->fetch_assoc();
        $turma_do_aluno_selecionado = $ocorrencia['id_turma'];
    }
    $stmt->close();
}

// --- PROCESSAMENTO DO FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_ocorrencia = $_POST['id_ocorrencia'] ?: null;
    $id_aluno = $_POST['id_aluno'];
    $data = $_POST['data_ocorrencia'];
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    // O id_registrado_por virá do utilizador logado
    $id_registrado_por = $_SESSION['id_usuario']; 

    if ($id_ocorrencia) {
        $stmt = $conexao->prepare("UPDATE ocorrencias SET id_aluno = ?, data_ocorrencia = ?, tipo = ?, descricao = ? WHERE id_ocorrencia = ?");
        $stmt->bind_param("isssi", $id_aluno, $data, $tipo, $descricao, $id_ocorrencia);
    } else {
        $stmt = $conexao->prepare("INSERT INTO ocorrencias (id_aluno, data_ocorrencia, tipo, descricao, id_registrado_por) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $id_aluno, $data, $tipo, $descricao, $id_registrado_por);
    }

    if ($stmt->execute()) {
        header("Location: Listagem_Ocorrencia.php?sucesso=Ocorrência salva com sucesso!");
    } else {
        header("Location: Cadastro_Ocorrencia.php?erro=Erro ao salvar ocorrência.");
    }
    $stmt->close();
    exit();
}

// --- BUSCA DE DADOS PARA DROPDOWNS ---
$turmas_result = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma");

$alunos_result = $conexao->query("SELECT id_aluno, nome_completo, id_turma FROM alunos ORDER BY nome_completo");
$alunos_por_turma = [];
if ($alunos_result->num_rows > 0) {
    while($aluno_item = $alunos_result->fetch_assoc()) {
        $alunos_por_turma[$aluno_item['id_turma']][] = $aluno_item;
    }
}
?>

<div class="form-container">
    <form method="POST" action="Cadastro_Ocorrencia.php<?php echo $is_edit_mode ? '?id=' . htmlspecialchars($ocorrencia['id_ocorrencia']) : ''; ?>">
        <input type="hidden" name="id_ocorrencia" value="<?php echo htmlspecialchars($ocorrencia['id_ocorrencia']); ?>">
        
        <h3 class="section-title">Dados da Ocorrência</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="turma_select">Filtrar por Turma*</label>
                <select id="turma_select">
                    <option value="">Selecione uma turma primeiro</option>
                    <?php if ($turmas_result->num_rows > 0) {
                        $turmas_result->data_seek(0);
                        while($turma = $turmas_result->fetch_assoc()): ?>
                            <option value="<?php echo $turma['id_turma']; ?>" <?php echo ($turma_do_aluno_selecionado == $turma['id_turma']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($turma['nome_turma']); ?>
                            </option>
                        <?php endwhile; } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_aluno">Aluno*</label>
                <select id="id_aluno" name="id_aluno" required <?php echo $is_edit_mode ? '' : 'disabled'; ?>>
                    <option value="">Selecione a turma para ver os alunos</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="data_ocorrencia">Data da Ocorrência*</label>
                <input type="datetime-local" id="data_ocorrencia" name="data_ocorrencia" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($ocorrencia['data_ocorrencia']))); ?>" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo de Ocorrência*</label>
                <select id="tipo" name="tipo" required>
                    <option value="Saúde" <?php echo (($ocorrencia['tipo']) == 'Saúde') ? 'selected' : ''; ?>>Saúde</option>
                    <option value="Comportamento" <?php echo (($ocorrencia['tipo']) == 'Comportamento') ? 'selected' : ''; ?>>Comportamento</option>
                    <option value="Incidente" <?php echo (($ocorrencia['tipo']) == 'Incidente') ? 'selected' : ''; ?>>Incidente</option>
                    <option value="Pedagógico" <?php echo (($ocorrencia['tipo']) == 'Pedagógico') ? 'selected' : ''; ?>>Pedagógico</option>
                    <option value="Outro" <?php echo (($ocorrencia['tipo']) == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição*</label>
            <textarea id="descricao" name="descricao" placeholder="Descreva a ocorrência detalhadamente" required><?php echo htmlspecialchars($ocorrencia['descricao']); ?></textarea>
        </div>
        <div class="form-actions">
            <a href="Listagem_Ocorrencia.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Ocorrência</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const alunosPorTurma = <?php echo json_encode($alunos_por_turma); ?>;
    const turmaSelect = document.getElementById('turma_select');
    const alunoSelect = document.getElementById('id_aluno');
    
    function popularAlunos() {
        const turmaId = turmaSelect.value;
        alunoSelect.innerHTML = '<option value="">Selecione o aluno</option>';

        if (turmaId && alunosPorTurma[turmaId]) {
            alunoSelect.disabled = false;
            alunosPorTurma[turmaId].forEach(function(aluno) {
                const option = document.createElement('option');
                option.value = aluno.id_aluno;
                option.textContent = aluno.nome_completo;
                alunoSelect.appendChild(option);
            });
        } else {
            alunoSelect.disabled = true;
        }
    }

    turmaSelect.addEventListener('change', popularAlunos);
    
    <?php if ($is_edit_mode && $turma_do_aluno_selecionado): ?>
        popularAlunos();
        alunoSelect.value = "<?php echo htmlspecialchars($ocorrencia['id_aluno']); ?>";
    <?php endif; ?>
});
</script>

<?php require_once PROJECT_ROOT . '/visao_secretario/templates/footer_secretario.php'; ?>