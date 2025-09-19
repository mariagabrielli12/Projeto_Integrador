<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Cadastro de Atestado';
$page_icon = 'fas fa-file-medical';
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// Inicializa variáveis
$atestado = ['id_atestado' => null, 'id_aluno' => '', 'data_inicio' => date('Y-m-d'), 'data_fim' => date('Y-m-d'), 'motivo' => ''];
$turma_do_aluno_selecionado = null;
$is_edit_mode = false;

// --- MODO EDIÇÃO ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_edit_mode = true;
    $page_title = 'Editar Atestado';
    $id_atestado = $_GET['id'];
    
    $stmt = $conexao->prepare(
        "SELECT at.*, al.id_turma 
         FROM atestados at 
         JOIN alunos al ON at.id_aluno = al.id_aluno 
         WHERE at.id_atestado = ?"
    );
    $stmt->bind_param("i", $id_atestado);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $atestado = $result->fetch_assoc();
        $turma_do_aluno_selecionado = $atestado['id_turma'];
    }
    $stmt->close();
}

// --- PROCESSAMENTO DO FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_atestado = $_POST['id_atestado'] ?: null;
    $id_aluno = $_POST['id_aluno'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $motivo = $_POST['motivo'];

    if ($id_atestado) {
        $stmt = $conexao->prepare("UPDATE atestados SET id_aluno = ?, data_inicio = ?, data_fim = ?, motivo = ? WHERE id_atestado = ?");
        $stmt->bind_param("isssi", $id_aluno, $data_inicio, $data_fim, $motivo, $id_atestado);
    } else {
        $stmt = $conexao->prepare("INSERT INTO atestados (id_aluno, data_inicio, data_fim, motivo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_aluno, $data_inicio, $data_fim, $motivo);
    }

    if ($stmt->execute()) {
        header("Location: Listagem_Atestado.php?sucesso=Atestado salvo com sucesso!");
    } else {
        header("Location: Cadastro_Atestado.php?erro=Erro ao salvar atestado.");
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
    <form method="POST" action="Cadastro_Atestado.php<?php echo $is_edit_mode ? '?id=' . htmlspecialchars($atestado['id_atestado'] ?? '') : ''; ?>">
        <input type="hidden" name="id_atestado" value="<?php echo htmlspecialchars($atestado['id_atestado'] ?? ''); ?>">
        
        <h3 class="section-title">Dados do Atestado</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="turma_select">Filtrar por Turma*</label>
                <select id="turma_select">
                    <option value="">Selecione uma turma primeiro</option>
                    <?php if ($turmas_result->num_rows > 0) {
                        $turmas_result->data_seek(0);
                        while($turma = $turmas_result->fetch_assoc()): ?>
                            <option value="<?php echo $turma['id_turma']; ?>" <?php echo (($turma_do_aluno_selecionado ?? '') == $turma['id_turma']) ? 'selected' : ''; ?>>
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
            <div class="form-group"><label>Período (Início)*</label><input type="date" name="data_inicio" value="<?php echo htmlspecialchars($atestado['data_inicio'] ?? ''); ?>" required></div>
            <div class="form-group"><label>Período (Fim)*</label><input type="date" name="data_fim" value="<?php echo htmlspecialchars($atestado['data_fim'] ?? ''); ?>" required></div>
        </div>
        <div class="form-group">
            <label for="motivo">Motivo*</label>
            <textarea id="motivo" name="motivo" placeholder="Descreva o motivo do atestado" required><?php echo htmlspecialchars($atestado['motivo'] ?? ''); ?></textarea>
        </div>
        <div class="form-actions">
            <a href="Listagem_Atestado.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Atestado</button>
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
    
    <?php if ($is_edit_mode && !empty($turma_do_aluno_selecionado)): ?>
        popularAlunos();
        alunoSelect.value = "<?php echo htmlspecialchars($atestado['id_aluno'] ?? ''); ?>";
    <?php endif; ?>
});
</script>

<?php require_once PROJECT_ROOT . '/visao_secretario/templates/footer_secretario.php'; ?>