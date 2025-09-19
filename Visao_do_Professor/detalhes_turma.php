<?php
// Garante que o header e a conexão com o banco sejam incluídos
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

// --- LÓGICA DO BANCO DE DADOS ATUALIZADA ---

// 1. Pega o ID da turma da URL
$turma_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($turma_id === 0) {
    echo "<p>Erro: Turma não especificada.</p>";
    require_once 'templates/footer_professor.php';
    exit();
}

// 2. Busca as informações da turma específica
$turma_info = null;
$stmt_turma = $conexao->prepare("SELECT nome_turma FROM turmas WHERE id_turma = ?");
$stmt_turma->bind_param("i", $turma_id);
$stmt_turma->execute();
$result_turma = $stmt_turma->get_result();
if ($result_turma->num_rows > 0) {
    $turma_info = $result_turma->fetch_assoc();
}
$stmt_turma->close();

if (!$turma_info) {
    echo "<p>Erro: Turma não encontrada!</p>";
    require_once 'templates/footer_professor.php';
    exit();
}

// 3. Busca todos os alunos que pertencem a esta turma
$alunos = [];
$stmt_alunos = $conexao->prepare("SELECT id_aluno, nome_completo FROM alunos WHERE id_turma = ? ORDER BY nome_completo ASC");
$stmt_alunos->bind_param("i", $turma_id);
$stmt_alunos->execute();
$result_alunos = $stmt_alunos->get_result();
if ($result_alunos) {
    $alunos = $result_alunos->fetch_all(MYSQLI_ASSOC);
}
$stmt_alunos->close();
// --- FIM DA LÓGICA ---

// Define as variáveis da página para o header
$page_title = 'Detalhes da Turma: ' . htmlspecialchars($turma_info['nome_turma']);
$page_icon = 'fas fa-info-circle';
$breadcrumb = 'Portal do Professor > Turmas > Minhas Turmas > Detalhes';
?>

<div class="dashboard-card">
  <h4><i class="fas fa-users"></i> Alunos Matriculados em <?php echo htmlspecialchars($turma_info['nome_turma']); ?></h4>
  
  <?php if (empty($alunos)): ?>
    <p>Nenhum aluno encontrado para esta turma.</p>
  <?php else: ?>
    <div class="student-grid">
      <?php foreach ($alunos as $aluno): ?>
      <div class="student-card">
        <div class="student-avatar"><?php echo strtoupper(substr($aluno['nome_completo'], 0, 2)); ?></div>
        <p><?php echo htmlspecialchars($aluno['nome_completo']); ?></p>
        
        <a href="perfil_aluno.php?id=<?php echo $aluno['id_aluno']; ?>" class="btn-profile">Ver Perfil</a>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="form-actions" style="margin-top: 20px; justify-content: flex-start;">
    <a href="minhas_turmas.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar para Minhas Turmas</a>
  </div>
</div>

<?php
// Inclui o rodapé
require_once 'templates/footer_professor.php';
?>