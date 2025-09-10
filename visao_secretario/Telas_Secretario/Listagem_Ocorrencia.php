<?php
$page_title = 'Listagem de Ocorrências';
$page_icon = 'fas fa-exclamation-triangle';
require_once '../templates/header_secretario.php';

// Lógica de Exclusão
if (isset($_GET['delete_id'])) {
    $id_ocorrencia = $_GET['delete_id'];
    $stmt = $conexao->prepare("DELETE FROM ocorrencias WHERE id_ocorrencia = ?");
    $stmt->bind_param("i", $id_ocorrencia);
    if ($stmt->execute()) {
        header("Location: Listagem_Ocorrencia.php?sucesso=Ocorrência excluída com sucesso!");
    } else {
        header("Location: Listagem_Ocorrencia.php?erro=Erro ao excluir ocorrência.");
    }
    $stmt->close();
    exit();
}

// Consulta de Ocorrências
$sql = "SELECT o.id_ocorrencia, o.data_ocorrencia, o.descricao, o.tipo, a.nome_completo as nome_aluno
        FROM ocorrencias o
        JOIN alunos a ON o.id_aluno = a.id_aluno
        ORDER BY o.data_ocorrencia DESC";
$resultado = $conexao->query($sql);
?>

<div class="table-container">
    <div class="table-settings">
        <a href="Cadastro_Ocorrencia.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Registar Nova Ocorrência</a>
    </div>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="alert success" style="margin-top: 15px;"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Aluno</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($ocorrencia = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date("d/m/Y H:i", strtotime($ocorrencia['data_ocorrencia'])); ?></td>
                    <td><?php echo htmlspecialchars($ocorrencia['nome_aluno']); ?></td>
                    <td><?php echo htmlspecialchars($ocorrencia['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($ocorrencia['descricao']); ?></td>
                    <td class="action-buttons">
                        <a href="Cadastro_Ocorrencia.php?id=<?php echo $ocorrencia['id_ocorrencia']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                        <a href="Listagem_Ocorrencia.php?delete_id=<?php echo $ocorrencia['id_ocorrencia']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhuma ocorrência registada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../templates/footer_secretario.php'; ?>