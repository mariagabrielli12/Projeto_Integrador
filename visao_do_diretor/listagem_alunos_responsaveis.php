<?php
define('VIEW_ROOT', __DIR__); 
define('PROJECT_ROOT', dirname(VIEW_ROOT)); 

$page_title = 'Alunos e Responsáveis';
$page_icon = 'fas fa-user-friends';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE FILTRO ---
$filtro_turma_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
$turmas = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma")->fetch_all(MYSQLI_ASSOC);

// --- LÓGICA DE CONSULTA ---
$sql = "
    SELECT a.id_aluno, a.nome_completo, a.data_nascimento, t.nome_turma, u.nome_completo as nome_responsavel
    FROM alunos a
    LEFT JOIN turmas t ON a.id_turma = t.id_turma
    LEFT JOIN usuarios u ON a.id_responsavel_principal = u.id_usuario
";
if ($filtro_turma_id > 0) {
    $sql .= " WHERE a.id_turma = $filtro_turma_id";
}
$sql .= " ORDER BY t.nome_turma, a.nome_completo";
$resultado = $conexao->query($sql);
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Filtro por Turma</h3></div>
    <div class="card-body">
        <form method="GET" action="listagem_alunos_responsaveis.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="turma_id">Selecione a Turma</label>
                    <select name="turma_id" id="turma_id" onchange="this.form.submit()">
                        <option value="0">Todas as Turmas</option>
                        <?php foreach ($turmas as $turma): ?>
                            <option value="<?php echo $turma['id_turma']; ?>" <?php echo ($filtro_turma_id == $turma['id_turma']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($turma['nome_turma']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Nome do Aluno</th>
                <th>Turma</th>
                <th>Responsável Principal</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($aluno = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_turma'] ?? 'Não matriculado'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_responsavel'] ?? 'Não vinculado'); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">Nenhum aluno encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>