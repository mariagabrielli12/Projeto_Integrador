<?php
// Garante que o header e a conexão com o banco sejam incluídos
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

// Define as variáveis da página
$page_title = 'Minhas Turmas';
$page_icon = 'fas fa-baby-carriage';
$breadcrumb = 'Portal do Professor > Turmas > Minhas Turmas';

// --- LÓGICA DO BANCO DE DADOS ATUALIZADA ---
$turmas = [];

// Consulta ATUALIZADA para buscar as turmas do professor logado
$sql = "
    SELECT 
        t.id_turma, 
        t.nome_turma, 
        t.turno,
        s.numero as numero_sala,
        u.nome_completo as nome_professor,
        -- Subconsulta ATUALIZADA para contar quantos alunos estão na turma
        (SELECT COUNT(*) FROM alunos WHERE id_turma = t.id_turma) as num_criancas
    FROM turmas t
    LEFT JOIN salas s ON t.id_sala = s.id_sala
    LEFT JOIN usuarios u ON t.id_professor = u.id_usuario
    WHERE t.id_professor = ?
    ORDER BY t.nome_turma ASC
";

if ($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param("i", $id_professor_logado);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado) {
        $turmas = $resultado->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}
// --- FIM DA LÓGICA ---
?>

<div class="welcome-banner">
  <h3>Minhas Turmas</h3>
  <p>Gerencie as turmas sob sua responsabilidade.</p>
</div>

<div class="class-grid">
    <?php if (empty($turmas)): ?>
        <p>Você não está associado a nenhuma turma no momento.</p>
    <?php else: ?>
        <?php foreach ($turmas as $turma): ?>
        <div class="class-card">
          <div class="class-header">
            <h3><?php echo htmlspecialchars($turma['nome_turma']); ?></h3>
            <span class="class-period"><?php echo htmlspecialchars($turma['turno']); ?></span>
          </div>
          <div class="class-body">
            <div class="class-info"><i class="fas fa-users"></i><span><?php echo $turma['num_criancas']; ?> criança(s) matriculada(s)</span></div>
            <div class="class-info"><i class="fas fa-user-tie"></i><span>Responsável: <?php echo htmlspecialchars($turma['nome_professor']); ?></span></div>
            <div class="class-info"><i class="fas fa-clock"></i><span>Período: <?php echo htmlspecialchars($turma['turno']); ?></span></div>
            <div class="class-info"><i class="fas fa-door-open"></i><span>Sala: <?php echo htmlspecialchars($turma['numero_sala'] ?? 'N/D'); ?></span></div>
          </div>
          <div class="class-footer">
            <a href="detalhes_turma.php?id=<?php echo $turma['id_turma']; ?>" class="btn btn-secondary">Ver Alunos</a>
            <a href="gerenciar_turma.php?id=<?php echo $turma['id_turma']; ?>" class="btn btn-primary">Gerenciar Diário</a>
          </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
// Inclui o rodapé
require_once 'templates/footer_professor.php';
?>