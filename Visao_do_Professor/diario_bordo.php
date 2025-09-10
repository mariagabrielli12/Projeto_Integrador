<?php
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

$page_title = 'Diário de Bordo';
$page_icon = 'fas fa-book';
$breadcrumb = 'Portal do Professor > Diário de Bordo';

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

// Busca os registros anteriores do diário de bordo
$registros_anteriores = [];
$sql_registros = "
    SELECT db.data_registro, db.titulo, db.observacoes, t.nome_turma
    FROM diario_bordo db
    JOIN turmas t ON db.id_turma = t.id_turma
    WHERE db.id_professor = ?
    ORDER BY db.data_registro DESC
";
$stmt_registros = $conexao->prepare($sql_registros);
$stmt_registros->bind_param("i", $id_professor_logado);
$stmt_registros->execute();
$result_registros = $stmt_registros->get_result();
if($result_registros) {
    $registros_anteriores = $result_registros->fetch_all(MYSQLI_ASSOC);
}
$stmt_registros->close();
?>

<div class="welcome-banner">
  <h3>Diário de Bordo da Turma</h3>
  <p>Registe as atividades diárias e observações gerais sobre o grupo.</p>
</div>

<div class="form-container">
    <form method="POST" action="processa_diario.php">
        <div class="form-row">
            <div class="form-group">
                <label for="turma_id">Turma*</label>
                <select id="turma_id" name="turma_id" required>
                    <option value="">Selecione a turma</option>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?php echo $turma['id_turma']; ?>"><?php echo htmlspecialchars($turma['nome_turma']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="data">Data*</label>
                <input type="date" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label for="titulo">Título do Registro*</label>
            <input type="text" id="titulo" name="titulo" placeholder="Ex: Atividade sensorial com texturas" required>
        </div>
        <div class="form-group">
            <label for="observacoes">Análise e Observações Gerais da Turma*</label>
            <textarea id="observacoes" name="observacoes" rows="5" placeholder="Descreva como a turma reagiu, participação, etc." required></textarea>
        </div>
        <div class="form-actions">
            <button class="btn btn-secondary" type="reset">Cancelar</button>
            <button class="btn btn-primary" type="submit">Salvar Registo</button>
        </div>
    </form>
</div>

<div class="table-container" style="margin-top: 30px;">
    <h4><i class="fas fa-history"></i> Registos Anteriores</h4>
    <div class="table-responsive">
        <table class="table">
          <thead>
            <tr><th>Data</th><th>Turma</th><th>Atividade</th><th>Observações</th><th>Ações</th></tr>
          </thead>
          <tbody>
            <?php if (empty($registros_anteriores)): ?>
                <tr><td colspan="5">Nenhum registo encontrado no diário de bordo.</td></tr>
            <?php else: ?>
                <?php foreach ($registros_anteriores as $reg): ?>
                <tr>
                  <td><?php echo date('d/m/Y', strtotime($reg['data_registro'])); ?></td>
                  <td><?php echo htmlspecialchars($reg['nome_turma']); ?></td>
                  <td><?php echo htmlspecialchars($reg['titulo']); ?></td>
                  <td><?php echo htmlspecialchars($reg['observacoes']); ?></td>
                  <td><button class="btn-icon" title="Editar"><i class="fas fa-edit"></i></button></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
    </div>
</div>

<?php
require_once 'templates/footer_professor.php';
?>