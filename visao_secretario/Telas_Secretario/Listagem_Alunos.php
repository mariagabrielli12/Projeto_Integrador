<?php
// Define a constante que aponta para a pasta raiz do projeto
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(dirname(__DIR__)));
}
// Define o título e o ícone da página
$page_title = 'Cadastro de Alunos';
$page_icon = 'fas fa-user-graduate';
// Inclui o cabeçalho do template
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// --- LÓGICA DE EXCLUSÃO ATUALIZADA ---
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id_para_deletar = $_GET['delete_id'];
    $stmt_delete = $conexao->prepare("DELETE FROM alunos WHERE id_aluno = ?");
    $stmt_delete->bind_param("i", $id_para_deletar);
    if ($stmt_delete->execute()) {
        header("Location: Listagem_Alunos.php?sucesso=Aluno excluído com sucesso!");
    } else {
        header("Location: Listagem_Alunos.php?erro=Erro ao excluir o aluno. Verifique as dependências.");
    }
    $stmt_delete->close();
    exit();
}

// --- LÓGICA DE CONSULTA ATUALIZADA ---
$sql = "SELECT a.id_aluno, a.nome_completo, a.cpf, a.contato_responsavel, t.nome_turma 
        FROM alunos a 
        LEFT JOIN turmas t ON a.id_turma = t.id_turma 
        ORDER BY a.nome_completo ASC";
$resultado = $conexao->query($sql);

if (!$resultado) {
    die("Erro na consulta SQL: " . $conexao->error);
}
?>

<div class="table-container">
    <div class="table-settings">
        <a href="Cadastro_Alunos.php" class="btn-cadastrar">
            <i class="fas fa-plus"></i> Cadastrar Novo Aluno
        </a>
    </div>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="alert success" style="margin-top: 15px;"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['erro'])): ?>
        <div class="alert error" style="margin-top: 15px;"><?php echo htmlspecialchars($_GET['erro']); ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Nome do Aluno</th>
                <th>CPF</th>
                <th>Contato do Responsável</th>
                <th>Turma</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while($aluno = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['cpf'] ?? 'N/D'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['contato_responsavel']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_turma'] ?? 'Sem turma'); ?></td>
                        <td class="action-buttons">
                            <a href="Cadastro_Alunos.php?id=<?php echo $aluno['id_aluno']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="Listagem_Alunos.php?delete_id=<?php echo $aluno['id_aluno']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum aluno cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once PROJECT_ROOT . '/visao_secretario/templates/footer_secretario.php';
?>