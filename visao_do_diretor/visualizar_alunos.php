<?php
// CORREÇÃO 1: Definimos a raiz da visão do diretor
define('VIEW_ROOT', __DIR__); 
// CORREÇÃO 2: A raiz do projeto está um nível acima
define('PROJECT_ROOT', dirname(VIEW_ROOT)); 

$page_title = 'Visualização de turmas';
$page_icon = 'fas fa-tachometer-alt';

// CORREÇÃO 3: Usamos o caminho correto para o header
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE CONSULTA ---
$turma_filtro_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;

// Busca todas as turmas para o filtro
$turmas = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma")->fetch_all(MYSQLI_ASSOC);

// Monta a query principal
$sql = "
    SELECT a.id_aluno, a.nome_completo, a.data_nascimento, t.nome_turma, u.nome_completo as nome_responsavel
    FROM alunos a
    LEFT JOIN turmas t ON a.id_turma = t.id_turma
    LEFT JOIN alunos_responsaveis ar ON a.id_aluno = ar.id_aluno
    LEFT JOIN usuarios u ON ar.id_responsavel = u.id_usuario
";

// Adiciona o filtro se uma turma foi selecionada
if ($turma_filtro_id > 0) {
    $sql .= " WHERE a.id_turma = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $turma_filtro_id);
} else {
    $stmt = $conexao->prepare($sql);
}

$stmt->execute();
$alunos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="filter-section" style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    <form method="GET" action="visualizar_alunos.php" style="display: flex; align-items: center; gap: 15px;">
        <div class="form-group" style="flex-grow: 1; margin-bottom: 0;">
            <label for="turma_id">Filtrar por Turma:</label>
            <select name="turma_id" id="turma_id" onchange="this.form.submit()">
                <option value="0">Todas as Turmas</option>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?php echo $turma['id_turma']; ?>" <?php echo ($turma_filtro_id == $turma['id_turma']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($turma['nome_turma']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <a href="visualizar_alunos.php" class="btn-secondary">Limpar Filtro</a>
    </form>
</div>

<div class="table-container">
    <table class="table">
        <thead><tr><th>Nome do Aluno</th><th>Data de Nascimento</th><th>Turma</th><th>Responsável Principal</th></tr></thead>
        <tbody>
            <?php if (empty($alunos)): ?>
                <tr><td colspan="4">Nenhum aluno encontrado.</td></tr>
            <?php else: ?>
                <?php foreach($alunos as $aluno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($aluno['data_nascimento'])); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_turma'] ?? 'Não matriculado'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_responsavel'] ?? 'N/D'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>