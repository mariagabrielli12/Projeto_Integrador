<?php
define('VIEW_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(VIEW_ROOT));

$page_title = 'Relatório de Saúde e Atestados';
$page_icon = 'fas fa-heartbeat';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DO FILTRO ---
$filtro_mes = isset($_GET['mes']) ? $_GET['mes'] : date('Y-m');
$nome_mes = date('F \de Y', strtotime($filtro_mes . '-01'));

// --- LÓGICA DO RELATÓRIO ---
$atestados_mes_query = "
    SELECT a.nome_completo, at.data_inicio, at.data_fim, at.motivo 
    FROM atestados at
    JOIN alunos a ON at.id_aluno = a.id_aluno
    WHERE DATE_FORMAT(at.data_inicio, '%Y-%m') = ?
    ORDER BY at.data_inicio DESC
";
$stmt_atestados = $conexao->prepare($atestados_mes_query);
$stmt_atestados->bind_param("s", $filtro_mes);
$stmt_atestados->execute();
$atestados_mes = $stmt_atestados->get_result()->fetch_all(MYSQLI_ASSOC);

// Contagem de atestados por motivo no mês
$motivos_frequentes_query = "
    SELECT motivo, COUNT(*) as total
    FROM atestados
    WHERE DATE_FORMAT(data_inicio, '%Y-%m') = ? AND motivo IS NOT NULL AND motivo != ''
    GROUP BY motivo ORDER BY total DESC LIMIT 5
";
$stmt_motivos = $conexao->prepare($motivos_frequentes_query);
$stmt_motivos->bind_param("s", $filtro_mes);
$stmt_motivos->execute();
$motivos_frequentes = $stmt_motivos->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Filtro por Mês</h3></div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="mes">Selecione o Mês</label>
                    <input type="month" id="mes" name="mes" class="form-control" value="<?php echo htmlspecialchars($filtro_mes); ?>">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="table-container">
    <div class="table-settings">
        <h3 class="section-title" style="margin: 0;">Atestados Médicos Recebidos em <?php echo $nome_mes; ?></h3>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Início do Afastamento</th>
                <th>Fim do Afastamento</th>
                <th>Motivo Declarado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($atestados_mes)): ?>
                <tr><td colspan="4">Nenhum atestado recebido neste mês.</td></tr>
            <?php else: ?>
                <?php foreach ($atestados_mes as $atestado): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($atestado['nome_completo']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($atestado['data_inicio'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($atestado['data_fim'])); ?></td>
                        <td><?php echo htmlspecialchars($atestado['motivo']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="dashboard-card" style="margin-top: 20px;">
    <div class="card-header"><h4><i class="fas fa-chart-pie"></i> Motivos Mais Frequentes (<?php echo $nome_mes; ?>)</h4></div>
    <div class="card-body"><canvas id="motivosChart"></canvas></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('motivosChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($motivos_frequentes, 'motivo')); ?>,
            datasets: [{
                label: 'Total de Atestados',
                data: <?php echo json_encode(array_column($motivos_frequentes, 'total')); ?>,
                backgroundColor: ['#e74c3c', '#3498db', '#f1c40f', '#2ecc71', '#9b59b6']
            }]
        }
    });
});
</script>

<?php require_once VIEW_ROOT . '/templates/footer.php'; ?>