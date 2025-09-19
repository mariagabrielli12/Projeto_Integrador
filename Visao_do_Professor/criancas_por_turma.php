<?php
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

$page_title = 'Crianças por Turma';
$page_icon = 'fas fa-user-friends';
$breadcrumb = 'Portal do Professor > Turmas > Crianças por Turma';

// Busca todos os alunos de todas as turmas do professor logado
$alunos = [];
$sql = "
    SELECT a.id_aluno, a.nome_completo, a.data_nascimento, t.nome_turma, u.nome_completo as nome_responsavel, u.telefone
    FROM alunos a
    JOIN turmas t ON a.id_turma = t.id_turma
    LEFT JOIN alunos_responsaveis ar ON a.id_aluno = ar.id_aluno
    LEFT JOIN usuarios u ON ar.id_responsavel = u.id_usuario
    WHERE t.id_professor = ?
    ORDER BY t.nome_turma, a.nome_completo
";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_professor_logado);
$stmt->execute();
$result = $stmt->get_result();
if($result) {
    $alunos = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();
?>

<div class="welcome-banner">
    <h3>Crianças por Turma</h3>
    <p>Visualize as informações das crianças matriculadas em suas turmas.</p>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Idade</th>
              <th>Turma</th>
              <th>Responsável</th>
              <th>Contato</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($alunos)): ?>
                <tr><td colspan="6">Nenhum aluno encontrado em suas turmas.</td></tr>
            <?php else: ?>
                <?php foreach($alunos as $aluno): 
                    $idade = 'N/D';
                    if ($aluno['data_nascimento']) {
                        $nasc = new DateTime($aluno['data_nascimento']);
                        $hoje = new DateTime();
                        $diff = $hoje->diff($nasc);
                        $idade = $diff->y . ' anos';
                    }
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                  <td><?php echo $idade; ?></td>
                  <td><?php echo htmlspecialchars($aluno['nome_turma']); ?></td>
                  <td><?php echo htmlspecialchars($aluno['nome_responsavel'] ?? 'N/D'); ?></td>
                  <td><?php echo htmlspecialchars($aluno['telefone'] ?? 'N/D'); ?></td>
                  <td>
                    <a href="perfil_aluno.php?id=<?php echo $aluno['id_aluno']; ?>" class="btn-icon" title="Ver Perfil Completo"><i class="fas fa-user"></i></a>
                  </td>
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