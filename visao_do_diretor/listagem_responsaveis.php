<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Listagem de Responsáveis';
$page_icon = 'fas fa-user-shield';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['excluir_id'])) {
    $id_para_excluir = (int)$_GET['excluir_id'];
    // A regra ON DELETE CASCADE no BD irá apagar as ligações em 'alunos_responsaveis' e 'enderecos'.
    $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id_usuario = ? AND id_tipo = 5");
    $stmt->bind_param("i", $id_para_excluir);
    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = "Responsável excluído com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao excluir o responsável.";
    }
    $stmt->close();
    header("Location: listagem_responsaveis.php");
    exit();
}

// --- LÓGICA DE CONSULTA ---
// id_tipo = 5 para 'Responsavel'
$sql = "SELECT id_usuario, nome_completo, cpf, email, telefone, ativo FROM usuarios WHERE id_tipo = 5 ORDER BY nome_completo ASC";
$resultado = $conexao->query($sql);
?>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Nome do Responsável</th>
                <th>CPF</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($responsavel = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($responsavel['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($responsavel['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($responsavel['email']); ?></td>
                        <td><?php echo htmlspecialchars($responsavel['telefone']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $responsavel['ativo'] ? 'active' : 'inactive'; ?>">
                                <?php echo $responsavel['ativo'] ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="editar_usuario.php?id=<?php echo $responsavel['id_usuario']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="listagem_responsaveis.php?excluir_id=<?php echo $responsavel['id_usuario']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem a certeza que deseja excluir este responsável?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Nenhum responsável encontrado no sistema.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once PROJECT_ROOT . '/visao_do_diretor/templates/footer_diretor.php';
?>