<?php
$page_title = 'Cadastro de Turmas';
$page_icon = 'fas fa-chalkboard-teacher';
require_once '../templates/header_secretario.php';

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['delete_id'])) {
    $id_turma = $_GET['delete_id'];
    $stmt = $conexao->prepare("DELETE FROM turmas WHERE id_turma = ?");
    $stmt->bind_param("i", $id_turma);
    if ($stmt->execute()) {
        header("Location: Listagem_Turma.php?sucesso=Turma excluída com sucesso!");
    } else {
        header("Location: Listagem_Turma.php?erro=Erro ao excluir turma. Verifique se há alunos matriculados nela.");
    }
    $stmt->close();
    exit();
}

// --- LÓGICA DE CONSULTA ---
$sql = "SELECT t.id_turma, t.nome_turma, t.turno, s.numero as numero_sala, u.nome_completo as nome_professor
        FROM turmas t
        LEFT JOIN salas s ON t.id_sala = s.id_sala
        LEFT JOIN usuarios u ON t.id_professor = u.id_usuario
        ORDER BY t.nome_turma";
$resultado = $conexao->query($sql);
?>

<div class="table-container">
    <div class="table-settings">
        <a href="Cadastro_Turma.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Cadastrar Nova Turma</a>
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
                <th>Nome da Turma</th>
                <th>Turno</th>
                <th>Sala</th>
                <th>Professor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($turma = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($turma['nome_turma']); ?></td>
                        <td><?php echo htmlspecialchars($turma['turno']); ?></td>
                        <td>Sala <?php echo htmlspecialchars($turma['numero_sala'] ?? 'N/D'); ?></td>
                        <td><?php echo htmlspecialchars($turma['nome_professor'] ?? 'N/D'); ?></td>
                        <td class="action-buttons">
                            <a href="Cadastro_Turma.php?id=<?php echo $turma['id_turma']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="Listagem_Turma.php?delete_id=<?php echo $turma['id_turma']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhuma turma cadastrada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../templates/footer_secretario.php'; ?>