<?php
$page_title = 'Listagem de Atestados';
$page_icon = 'fas fa-file-medical';
require_once '../templates/header_secretario.php';

// --- LÓGICA DE FILTRO ---
$filtro_turma_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['delete_id'])) {
    $id_atestado = $_GET['delete_id'];
    $stmt = $conexao->prepare("DELETE FROM atestados WHERE id_atestado = ?");
    $stmt->bind_param("i", $id_atestado);
    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = "Atestado excluído com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao excluir o atestado.";
    }
    $stmt->close();
    header("Location: Listagem_Atestado.php");
    exit();
}

// --- LÓGICA DE CONSULTA COM FILTRO ---
$sql = "SELECT atestado.id_atestado, aluno.nome_completo as nome_aluno, atestado.data_inicio, atestado.data_fim, atestado.motivo
        FROM atestados as atestado
        JOIN alunos as aluno ON atestado.id_aluno = aluno.id_aluno";
        
if ($filtro_turma_id > 0) {
    $sql .= " WHERE aluno.id_turma = ?";
}
$sql .= " ORDER BY atestado.data_inicio DESC";

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
        <a href="Cadastro_Atestado.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Cadastrar Novo Atestado</a>
    </div>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Data Início</th>
                <th>Data Fim</th>
                <th>Motivo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($atestado = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($atestado['nome_aluno']); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($atestado['data_inicio'])); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($atestado['data_fim'])); ?></td>
                        <td><?php echo htmlspecialchars($atestado['motivo']); ?></td>
                        <td class="action-buttons">
                            <a href="Cadastro_Atestado.php?id=<?php echo $atestado['id_atestado']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="Listagem_Atestado.php?delete_id=<?php echo $atestado['id_atestado']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum atestado encontrado para a turma selecionada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../templates/footer_secretario.php'; ?>