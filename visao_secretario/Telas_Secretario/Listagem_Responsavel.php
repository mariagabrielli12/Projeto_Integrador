<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Cadastro de Responsáveis';
$page_icon = 'fas fa-user-tie';
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// --- LÓGICA DE EXCLUSÃO ATUALIZADA ---
// Graças ao ON DELETE CASCADE no banco, só precisamos de apagar o registo da tabela 'usuarios'.
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id_para_deletar = $_GET['delete_id'];
    
    $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_para_deletar);
    if ($stmt->execute()) {
        header("Location: Listagem_Responsavel.php?sucesso=Responsável excluído com sucesso!");
    } else {
        header("Location: Listagem_Responsavel.php?erro=Erro ao excluir responsável.");
    }
    $stmt->close();
    exit();
}

// --- LÓGICA DE CONSULTA ATUALIZADA ---
// id_tipo = 5 para 'Responsavel'
$sql = "SELECT id_usuario, nome_completo, cpf, email, telefone FROM usuarios WHERE id_tipo = 5 ORDER BY nome_completo ASC";
$resultado = $conexao->query($sql);

if (!$resultado) {
    die("Erro na consulta SQL: " . $conexao->error);
}
?>

<div class="table-container">
    <div class="table-settings">
        <a href="Cadastro_Responsavel.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Cadastrar Novo Responsável</a>
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
                <th>Nome</th>
                <th>CPF</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while($responsavel = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($responsavel['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($responsavel['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($responsavel['email']); ?></td>
                        <td><?php echo htmlspecialchars($responsavel['telefone'] ?? 'N/D'); ?></td>
                        <td class="action-buttons">
                            <a href="Cadastro_Responsavel.php?id=<?php echo $responsavel['id_usuario']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="Listagem_Responsavel.php?delete_id=<?php echo $responsavel['id_usuario']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza? Apagar um responsável também o removerá de todos os alunos associados.');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum responsável cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once PROJECT_ROOT . '/visao_secretario/templates/footer_secretario.php'; ?>