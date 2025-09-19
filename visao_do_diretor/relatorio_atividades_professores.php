<?php
define('VIEW_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(VIEW_ROOT));

$page_title = 'Relatório de Atividades por Professor';
$page_icon = 'fas fa-tasks';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE FILTROS ---
$filtro_professor_id = isset($_GET['professor_id']) ? (int)$_GET['professor_id'] : 0;
$professores = $conexao->query("SELECT id_usuario, nome_completo FROM usuarios WHERE id_tipo = 3 ORDER BY nome_completo")->fetch_all(MYSQLI_ASSOC);

// --- LÓGICA DE CONSULTA ---
// Esta consulta une registos de diferentes tabelas para uma visão consolidada
$where_clause = "";
if ($filtro_professor_id > 0) {
    $where_clause = "WHERE u.id_usuario = $filtro_professor_id";
}

$sql = "
    (SELECT 
        u.nome_completo as professor, 
        db.data_registro as data, 
        'Diário de Bordo' as tipo, 
        db.titulo as detalhe,
        t.nome_turma
    FROM diario_bordo db
    JOIN usuarios u ON db.id_professor = u.id_usuario
    JOIN turmas t ON db.id_turma = t.id_turma
    $where_clause
    )
    UNION ALL
    (SELECT 
        u.nome_completo as professor, 
        do.data_observacao as data, 
        'Desenvolvimento' as tipo, 
        CONCAT(do.area_desenvolvimento, ': ', do.habilidade_observada) as detalhe,
        a.nome_completo as nome_turma_aluno
    FROM desenvolvimento_observacoes do
    JOIN usuarios u ON do.id_professor = u.id_usuario
    JOIN alunos a ON do.id_aluno = a.id_aluno
    $where_clause
    )
    ORDER BY data DESC
";

$atividades = $conexao->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Filtros do Relatório</h3></div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Filtrar por Professor</label>
                    <select name="professor_id" class="form-control" onchange="this.form.submit()">
                        <option value="0">Todos os Professores</option>
                        <?php foreach($professores as $prof): ?>
                            <option value="<?php echo $prof['id_usuario']; ?>" <?php echo ($filtro_professor_id == $prof['id_usuario']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prof['nome_completo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-container" style="margin-top: 20px;">
    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Professor</th>
                <th>Tipo de Registro</th>
                <th>Turma / Aluno</th>
                <th>Detalhe</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($atividades)): ?>
                <?php foreach($atividades as $atividade): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($atividade['data'])); ?></td>
                        <td><?php echo htmlspecialchars($atividade['professor']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $atividade['tipo'] == 'Diário de Bordo' ? 'active' : 'inactive'; ?>">
                                <?php echo htmlspecialchars($atividade['tipo']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($atividade['nome_turma']); ?></td>
                        <td><?php echo htmlspecialchars($atividade['detalhe']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhuma atividade encontrada para os filtros selecionados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once VIEW_ROOT . '/templates/footer.php'; ?>