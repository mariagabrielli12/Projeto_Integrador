<?php
define('VIEW_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(VIEW_ROOT));

$page_title = 'Relatório de Frequência Consolidado';
$page_icon = 'fas fa-chart-bar';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DO RELATÓRIO ---
$turma_filtro_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
$turmas = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma")->fetch_all(MYSQLI_ASSOC);

$where_clause_turma = $turma_filtro_id > 0 ? "AND t.id_turma = $turma_filtro_id" : "";

// 1. Média de Frequência Geral
$media_geral_result = $conexao->query("
    SELECT AVG(CASE WHEN presenca = 'presente' THEN 1 ELSE 0 END) * 100 as media
    FROM registro_presenca
    WHERE data >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
")->fetch_assoc();
$media_geral = $media_geral_result['media'] ?? 0;

// 2. Frequência por Turma
$frequencia_por_turma_query = "
    SELECT 
        t.nome_turma,
        AVG(CASE WHEN rp.presenca = 'presente' THEN 1 ELSE 0 END) * 100 as media_frequencia
    FROM turmas t
    LEFT JOIN registro_presenca rp ON t.id_turma = rp.id_turma
    WHERE rp.data >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY t.id_turma
    ORDER BY media_frequencia ASC
";
$frequencia_por_turma = $conexao->query($frequencia_por_turma_query)->fetch_all(MYSQLI_ASSOC);

// 3. Alunos com Baixa Frequência
$alunos_baixa_frequencia_query = "
    SELECT 
        a.nome_completo,
        t.nome_turma,
        AVG(CASE WHEN rp.presenca = 'presente' THEN 1 ELSE 0 END) * 100 as media_frequencia,
        COUNT(CASE WHEN rp.presenca = 'ausente' THEN 1 END) as total_faltas
    FROM alunos a
    JOIN registro_presenca rp ON a.id_aluno = rp.id_aluno
    JOIN turmas t ON a.id_turma = t.id_turma
    WHERE rp.data >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) $where_clause_turma
    GROUP BY a.id_aluno, t.nome_turma
    HAVING media_frequencia < 75
    ORDER BY media_frequencia ASC
";
$alunos_baixa_frequencia = $conexao->query($alunos_baixa_frequencia_query)->fetch_all(MYSQLI_ASSOC);
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Filtros do Relatório</h3></div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-row">
                 <div class="form-group">
                    <label>Filtrar Alunos com Baixa Frequência por Turma</label>
                    <select name="turma_id" class="form-control" onchange="this.form.submit()">
                        <option value="0">Todas as Turmas</option>
                        <?php foreach($turmas as $turma): ?>
                            <option value="<?php echo $turma['id_turma']; ?>" <?php echo ($turma_filtro_id == $turma['id_turma']) ? 'selected' : ''; ?>><?php echo $turma['nome_turma']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="summary-cards">
    <div class="summary-card" style="background-color: #fff; border-left: 5px solid #2980b9;">
        <h3><?php echo number_format($media_geral, 1); ?>%</h3>
        <p>Média Geral de Frequência (Últimos 30 dias)</p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <h4><i class="fas fa-percent"></i> Frequência Média por Turma (%)</h4>
        <div class="card-body"><canvas id="frequenciaTurmaChart"></canvas></div>
    </div>
</div>

<div class="table-container" style="margin-top: 20px;">
    <div class="table-settings">
        <h3 class="section-title" style="margin: 0; color: #c0392b;"><i class="fas fa-exclamation-triangle"></i> Alunos com Frequência Abaixo de 75% (Últimos 30 dias)</h3>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Turma</th>
                <th>Total de Faltas</th>
                <th>Média de Frequência</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($alunos_baixa_frequencia)): ?>
                <tr><td colspan="4">Nenhum aluno com baixa frequência para o filtro selecionado.</td></tr>
            <?php else: ?>
                <?php foreach ($alunos_baixa_frequencia as $aluno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_turma']); ?></td>
                        <td><?php echo $aluno['total_faltas']; ?></td>
                        <td><?php echo number_format($aluno['media_frequencia'], 1); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('frequenciaTurmaChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($frequencia_por_turma, 'nome_turma')); ?>,
            datasets: [{
                label: 'Frequência Média (%)',
                data: <?php echo json_encode(array_column($frequencia_por_turma, 'media_frequencia')); ?>,
                backgroundColor: '#3498db'
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, max: 100 } },
            indexAxis: 'y',
        }
    });
});
</script>

<?php require_once VIEW_ROOT . '/templates/footer.php'; ?>