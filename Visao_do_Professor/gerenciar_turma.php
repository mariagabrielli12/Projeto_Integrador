<?php
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

// Pega o ID da turma da URL e valida
$turma_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($turma_id === 0) {
    echo "<p class='alert error'>Erro: Turma não especificada.</p>";
    require_once 'templates/footer_professor.php';
    exit();
}
$data_hoje = date('Y-m-d');

// Busca as informações da turma
$stmt_turma = $conexao->prepare("SELECT nome_turma FROM turmas WHERE id_turma = ?");
$stmt_turma->bind_param("i", $turma_id);
$stmt_turma->execute();
$turma_info = $stmt_turma->get_result()->fetch_assoc();
$stmt_turma->close();

if (!$turma_info) {
    echo "<p class='alert error'>Erro: Turma não encontrada!</p>";
    require_once 'templates/footer_professor.php';
    exit();
}

// Processa o formulário de salvamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $presencas = $_POST['status'] ?? [];
    $diarios = $_POST['diario'] ?? [];

    $conexao->begin_transaction();
    try {
        // Salva a presença
        $sql_presenca = "INSERT INTO registro_presenca (id_aluno, id_turma, data, presenca) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE presenca = VALUES(presenca)";
        $stmt_presenca = $conexao->prepare($sql_presenca);

        // Salva os registos diários
        $sql_diario = "INSERT INTO registros_diarios (id_aluno, id_professor, data, tipo_registro, descricao) VALUES (?, ?, ?, ?, ?)";
        $stmt_diario = $conexao->prepare($sql_diario);

        foreach ($presencas as $aluno_id => $status) {
            $stmt_presenca->bind_param("iiss", $aluno_id, $turma_id, $data_hoje, $status);
            $stmt_presenca->execute();

            if ($status === 'presente' && isset($diarios[$aluno_id])) {
                foreach ($diarios[$aluno_id] as $tipo => $descricao) {
                    if (!empty(trim($descricao))) {
                        $stmt_diario->bind_param("iisss", $aluno_id, $id_professor_logado, $data_hoje, $tipo, $descricao);
                        $stmt_diario->execute();
                    }
                }
            }
        }
        $stmt_presenca->close();
        $stmt_diario->close();
        $conexao->commit();
        $_SESSION['mensagem_sucesso'] = "Diário da turma salvo com sucesso!";
        header("Location: gerenciar_turma.php?id=" . $turma_id);
        exit();
    } catch (Exception $e) {
        $conexao->rollback();
        $_SESSION['mensagem_erro'] = "Erro ao salvar o diário: " . $e->getMessage();
    }
}

// Busca os alunos da turma e a presença já registada para hoje
$alunos = [];
$sql_alunos = "
    SELECT a.id_aluno, a.nome_completo, rp.presenca 
    FROM alunos a
    LEFT JOIN registro_presenca rp ON a.id_aluno = rp.id_aluno AND rp.data = ?
    WHERE a.id_turma = ? 
    ORDER BY a.nome_completo ASC
";
$stmt_alunos = $conexao->prepare($sql_alunos);
$stmt_alunos->bind_param("si", $data_hoje, $turma_id);
$stmt_alunos->execute();
$result_alunos = $stmt_alunos->get_result();
if ($result_alunos) {
    $alunos = $result_alunos->fetch_all(MYSQLI_ASSOC);
}
$stmt_alunos->close();

$page_title = 'Gerir Turma: ' . htmlspecialchars($turma_info['nome_turma']);
$page_icon = 'fas fa-tasks';
$breadcrumb = 'Portal do Professor > Turmas > Gerir Diário';
?>

<form method="POST" action="gerenciar_turma.php?id=<?php echo $turma_id; ?>">
  <div class="dashboard-card">
    <h4><i class="fas fa-user-check"></i> Chamada do Dia (<?php echo date('d/m/Y'); ?>)</h4>
    <table class="attendance-table">
      <thead><tr><th>Aluno</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach($alunos as $aluno): ?>
        <tr>
          <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
          <td>
            <label><input type="radio" name="status[<?php echo $aluno['id_aluno']; ?>]" value="presente" <?php echo ($aluno['presenca'] == 'presente') ? 'checked' : ''; ?> onchange="toggleDiario(<?php echo $aluno['id_aluno']; ?>, true)"> Presente</label>
            <label><input type="radio" name="status[<?php echo $aluno['id_aluno']; ?>]" value="ausente" <?php echo ($aluno['presenca'] == 'ausente') ? 'checked' : ''; ?> onchange="toggleDiario(<?php echo $aluno['id_aluno']; ?>, false)"> Ausente</label>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  
  <div class="dashboard-card" style="margin-top: 20px;">
      <h4><i class="fas fa-book-medical"></i> Registo Diário dos Alunos Presentes</h4>
      <div class="table-responsive">
        <table class="daily-log-table">
            <thead><tr><th>Aluno</th><th>Alimentação</th><th>Sono</th><th>Higiene</th><th>Observações</th></tr></thead>
            <tbody>
                <?php foreach($alunos as $aluno): ?>
                  <tr id="diario-aluno-<?php echo $aluno['id_aluno']; ?>">
                      <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                      <td><textarea name="diario[<?php echo $aluno['id_aluno']; ?>][Alimentação]" placeholder="Comeu bem, pouco..."></textarea></td>
                      <td><textarea name="diario[<?php echo $aluno['id_aluno']; ?>][Sono]" placeholder="Dormiu por 1h..."></textarea></td>
                      <td><textarea name="diario[<?php echo $aluno['id_aluno']; ?>][Higiene]" placeholder="Trocas de fralda..."></textarea></td>
                      <td><textarea name="diario[<?php echo $aluno['id_aluno']; ?>][Observações]" placeholder="Participou da atividade..."></textarea></td>
                  </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      </div>
  </div>

  <div class="form-actions" style="margin-top: 20px;">
      <a href="minhas_turmas.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Diário do Dia</button>
  </div>
</form>

<script>
function toggleDiario(alunoId, isPresente) {
    const tr = document.getElementById('diario-aluno-' + alunoId);
    const textareas = tr.querySelectorAll('textarea');
    if (isPresente) {
        tr.style.opacity = '1';
        textareas.forEach(textarea => textarea.disabled = false);
    } else {
        tr.style.opacity = '0.5';
        textareas.forEach(textarea => {
            textarea.disabled = true;
            textarea.value = '';
        });
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const presencas = document.querySelectorAll('input[name^="status"]');
    presencas.forEach(radio => {
        if (radio.checked) {
            const alunoId = radio.name.match(/\[(\d+)\]/)[1];
            toggleDiario(alunoId, radio.value === 'presente');
        }
    });
});
</script>

<?php
require_once 'templates/footer_professor.php';
?>