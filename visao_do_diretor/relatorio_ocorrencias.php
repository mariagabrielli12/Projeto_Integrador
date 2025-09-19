<?php
define('VIEW_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(VIEW_ROOT));

$page_title = 'Relatório de Ocorrências';
$page_icon = 'fas fa-exclamation-triangle';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE FILTROS E CONSULTA ---
$turmas = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma")->fetch_all(MYSQLI_ASSOC);

$sql = "
    SELECT o.data_ocorrencia, o.tipo, o.descricao, a.nome_completo as nome_aluno, t.nome_turma
    FROM ocorrencias o
    JOIN alunos a ON o.id_aluno = a.id_aluno
    JOIN turmas t ON a.id_turma = t.id_turma
    WHERE 1=1
";

if (!empty($_GET['data_inicio'])) {
    $sql .= " AND o.data_ocorrencia >= '" . $conexao->real_escape_string($_GET['data_inicio']) . "'";
}
if (!empty($_GET['data_fim'])) {
    $sql .= " AND o.data_ocorrencia <= '" . $conexao->real_escape_string($_GET['data_fim']) . " 23:59:59'";
}
if (!empty($_GET['tipo'])) {
    $sql .= " AND o.tipo = '" . $conexao->real_escape_string($_GET['tipo']) . "'";
}
if (!empty($_GET['turma_id'])) {
    $sql .= " AND a.id_turma = " . (int)$_GET['turma_id'];
}

$sql .= " ORDER BY o.data_ocorrencia DESC";
$resultado = $conexao->query($sql);
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Filtros do Relatório</h3></div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group"><label>Data Início:</label><input type="date" name="data_inicio" class="form-control" value="<?php echo $_GET['data_inicio'] ?? ''; ?>"></div>
                <div class="form-group"><label>Data Fim:</label><input type="date" name="data_fim" class="form-control" value="<?php echo $_GET['data_fim'] ?? ''; ?>"></div>
                <div class="form-group"><label>Tipo:</label><input type="text" name="tipo" class="form-control" value="<?php echo $_GET['tipo'] ?? ''; ?>"></div>
                <div class="form-group"><label>Turma:</label>
                    <select name="turma_id" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach($turmas as $turma): ?>
                            <option value="<?php echo $turma['id_turma']; ?>" <?php echo (($_GET['turma_id'] ?? '') == $turma['id_turma']) ? 'selected' : ''; ?>><?php echo $turma['nome_turma']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
    </div>
</div>

<div class="table-container" style="margin-top: 20px;">
    <table class="table">
        <thead>
            <tr>
                <th>Data</th><th>Aluno</th><th>Turma</th><th>Tipo</th><th>Descrição</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($ocorrencia = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($ocorrencia['data_ocorrencia'])); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['nome_aluno']); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['nome_turma']); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['tipo']); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['descricao']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhuma ocorrência encontrada para os filtros selecionados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once VIEW_ROOT . '/templates/footer.php'; ?>