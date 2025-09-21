<?php
// Define a constante que aponta para a pasta raiz do projeto
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(dirname(__DIR__)));
}
$page_title = 'Listagem de Alunos';
$page_icon = 'fas fa-user-graduate';
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// --- LÓGICA DE FILTRO ---
$filtro_turma_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    // ... (código de exclusão permanece o mesmo)
    $id_para_deletar = $_GET['delete_id'];
    $stmt_delete = $conexao->prepare("DELETE FROM alunos WHERE id_aluno = ?");
    $stmt_delete->bind_param("i", $id_para_deletar);
    if ($stmt_delete->execute()) {
        header("Location: Listagem_Alunos.php?sucesso=Aluno excluído com sucesso!");
    } else {
        header("Location: Listagem_Alunos.php?erro=Erro ao excluir o aluno.");
    }
    $stmt_delete->close();
    exit();
}

// --- LÓGICA DE CONSULTA COM FILTRO ---
$sql = "SELECT a.id_aluno, a.nome_completo, a.cpf, t.nome_turma 
        FROM alunos a 
        LEFT JOIN turmas t ON a.id_turma = t.id_turma";

if ($filtro_turma_id > 0) {
    $sql .= " WHERE a.id_turma = ?";
}
$sql .= " ORDER BY a.nome_completo ASC";

$stmt = $conexao->prepare($sql);
if ($filtro_turma_id > 0) {
    $stmt->bind_param("i", $filtro_turma_id);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Busca todas as turmas para o dropdown do filtro
$turmas = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma")->fetch_all(MYSQLI_ASSOC);

?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Filtro de Busca</h3></div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="turma_id">Filtrar por Turma</label>
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


<div class="table-container" style="margin-top: 20px;">
    <div class="table-settings">
        <a href="Cadastro_Alunos.php" class="btn-cadastrar">
            <i class="fas fa-plus"></i> Cadastrar Novo Aluno
        </a>
    </div>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['erro'])): ?>
        <div class="alert error"><?php echo htmlspecialchars($_GET['erro']); ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Nome do Aluno</th>
                <th>CPF</th>
                <th>Turma</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($aluno = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['cpf'] ?? 'N/D'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_turma'] ?? 'Sem turma'); ?></td>
                        <td class="action-buttons">
                            <a href="Cadastro_Alunos.php?id=<?php echo $aluno['id_aluno']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="Listagem_Alunos.php?delete_id=<?php echo $aluno['id_aluno']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhum aluno encontrado para a turma selecionada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer_secretario.php';
?>