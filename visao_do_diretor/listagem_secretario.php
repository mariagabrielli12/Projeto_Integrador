<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Listagem de Secretários';
$page_icon = 'fas fa-user-tie';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE CONSULTA ---
// id_tipo = 2 para 'Secretario'
$sql = "SELECT id_usuario, nome_completo, cpf, telefone, ativo FROM usuarios WHERE id_tipo = 2 ORDER BY nome_completo ASC";
$resultado = $conexao->query($sql);
?>

<div class="table-container">
    <div class="table-settings">
        <a href="index.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Cadastrar Novo Secretário</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Telefone</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($secretario = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($secretario['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($secretario['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($secretario['telefone']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $secretario['ativo'] ? 'active' : 'inactive'; ?>">
                                <?php echo $secretario['ativo'] ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="#" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn-icon" title="Excluir"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum secretário cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once PROJECT_ROOT . '/visao_do_diretor/templates/footer_diretor.php';
?>