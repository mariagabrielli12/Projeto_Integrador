<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Listagem de Berçaristas';
$page_icon = 'fas fa-baby';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE CONSULTA ---
// id_tipo = 4 para 'Bercarista'
$sql = "SELECT id_usuario, nome_completo, telefone, matricula, ativo FROM usuarios WHERE id_tipo = 4 ORDER BY nome_completo ASC";
$resultado = $conexao->query($sql);
?>

<div class="table-container">
    <div class="table-settings">
        <a href="index.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Cadastrar Novo Berçarista</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Telefone</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($bercarista = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bercarista['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($bercarista['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($bercarista['telefone']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $bercarista['ativo'] ? 'active' : 'inactive'; ?>">
                                <?php echo $bercarista['ativo'] ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="#" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn-icon" title="Excluir"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum berçarista cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once PROJECT_ROOT . '/visao_do_diretor/templates/footer_diretor.php';
?>