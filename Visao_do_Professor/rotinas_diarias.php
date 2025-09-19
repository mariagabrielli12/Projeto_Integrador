<?php
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

$page_title = 'Rotinas Diárias';
$page_icon = 'fas fa-clipboard-list';
$breadcrumb = 'Portal do Professor > Planeamento > Rotinas Diárias';

// Busca as turmas do professor para os formulários
$turmas = [];
$stmt_turmas = $conexao->prepare("SELECT id_turma, nome_turma FROM turmas WHERE id_professor = ? ORDER BY nome_turma");
$stmt_turmas->bind_param("i", $id_professor_logado);
$stmt_turmas->execute();
$result_turmas = $stmt_turmas->get_result();
if ($result_turmas) {
    $turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);
}
$stmt_turmas->close();
?>

<div class="welcome-banner">
  <h3>Rotinas Diárias</h3>
  <p>Gira os horários e atividades padrão para cada turma.</p>
</div>

<div class="form-container" style="margin-top: 20px;">
    <h4><i class="fas fa-edit"></i> Cadastrar ou Editar Rotina</h4>
    <form method="POST" action="processa_rotina.php">
        <div class="form-row">
            <div class="form-group">
                <label>Turma</label>
                <select name="turma_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?php echo $turma['id_turma']; ?>"><?php echo htmlspecialchars($turma['nome_turma']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dia da semana</label>
                <select name="dia_semana" required>
                    <option value="Segunda-feira">Segunda-feira</option>
                    <option value="Terça-feira">Terça-feira</option>
                    <option value="Quarta-feira">Quarta-feira</option>
                    <option value="Quinta-feira">Quinta-feira</option>
                    <option value="Sexta-feira">Sexta-feira</option>
                </select>
            </div>
        </div>
        
        <div id="routine-items">
            <div class="routine-item">
                <input type="time" name="horario_inicio[]" required>
                <span>até</span>
                <input type="time" name="horario_fim[]" required>
                <input type="text" name="atividade_descricao[]" placeholder="Descrição da atividade" required>
                <button type="button" class="btn-icon" onclick="this.parentElement.remove();"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        
        <button type="button" class="btn btn-secondary" style="margin-top: 10px;" onclick="adicionarItemRotina()">
          <i class="fas fa-plus"></i> Adicionar Item
        </button>
        
        <div class="form-actions">
          <button class="btn btn-secondary" type="reset">Cancelar</button>
          <button class="btn btn-primary" type="submit">Salvar Rotina do Dia</button>
        </div>
    </form>
</div>

<?php
$extra_js = '<script>
function adicionarItemRotina() {
    const container = document.getElementById("routine-items");
    const novoItem = document.createElement("div");
    novoItem.className = "routine-item";
    novoItem.innerHTML = `
        <input type="time" name="horario_inicio[]" required>
        <span>até</span>
        <input type="time" name="horario_fim[]" required>
        <input type="text" name="atividade_descricao[]" placeholder="Descrição da atividade" required>
        <button type="button" class="btn-icon" onclick="this.parentElement.remove();"><i class="fas fa-trash"></i></button>
    `;
    container.appendChild(novoItem);
}
</script>';
require_once 'templates/footer_professor.php';
?>