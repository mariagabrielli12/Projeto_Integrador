<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Listagem de Professores';
$page_icon = 'fas fa-chalkboard-teacher';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['excluir_id'])) {
    $id_para_excluir = (int)$_GET['excluir_id'];
    $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_para_excluir);
    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = "Utilizador excluído com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao excluir o utilizador.";
    }
    $stmt->close();
    header("Location: listagem_professor.php");
    exit();
}


// --- LÓGICA DE CONSULTA ---
// id_tipo = 3 para 'Professor'
$sql = "SELECT id_usuario, nome_completo, cpf, email, ativo FROM usuarios WHERE id_tipo = 3 ORDER BY nome_completo ASC";
$resultado = $conexao->query($sql);
?>

<div class="table-container">
    <div class="table-settings">
        <a href="index.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Cadastrar Novo Professor</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Email</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($professor = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($professor['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($professor['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($professor['email']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $professor['ativo'] ? 'active' : 'inactive'; ?>">
                                <?php echo $professor['ativo'] ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="editar_usuario.php?id=<?php echo $professor['id_usuario']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="listagem_professor.php?excluir_id=<?php echo $professor['id_usuario']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem a certeza que deseja excluir este utilizador?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum professor cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once PROJECT_ROOT . '/visao_do_diretor/templates/footer_diretor.php';
?>