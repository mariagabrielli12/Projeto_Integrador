<?php
$page_title = 'Cadastro de Salas';
$page_icon = 'fas fa-door-open';
require_once '../templates/header_secretario.php';

// Lógica de Exclusão
if (isset($_GET['delete_id'])) {
    $id_sala = $_GET['delete_id'];
    $stmt = $conexao->prepare("DELETE FROM salas WHERE id_sala = ?");
    $stmt->bind_param("i", $id_sala);
    if ($stmt->execute()) {
        header("Location: Listagem_Sala.php?sucesso=Sala excluída com sucesso!");
    } else {
        header("Location: Listagem_Sala.php?erro=Erro ao excluir sala. Verifique se ela não está em uso por uma turma.");
    }
    $stmt->close();
    exit();
}

$salas = $conexao->query("SELECT * FROM salas ORDER BY numero ASC");
?>

<div class="table-container">
    <div class="table-settings">
        <a href="Cadastro_Salas.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Cadastrar Nova Sala</a>
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
                <th>Número</th>
                <th>Capacidade</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($salas && $salas->num_rows > 0): ?>
                <?php while($sala = $salas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sala['numero']); ?></td>
                        <td><?php echo htmlspecialchars($sala['capacidade']); ?></td>
                        <td><span class="status-badge active"><?php echo htmlspecialchars($sala['status']); ?></span></td>
                        <td class="action-buttons">
                            <a href="Cadastro_Salas.php?id=<?php echo $sala['id_sala']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="Listagem_Sala.php?delete_id=<?php echo $sala['id_sala']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhuma sala cadastrada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../templates/footer_secretario.php'; ?>