<?php
define('VIEW_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(VIEW_ROOT));

$page_title = 'Relatório de Matrículas';
$page_icon = 'fas fa-user-check';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DO RELATÓRIO ---

// Usamos a data de nascimento como proxy para a data de matrícula, pegando os mais novos
// O ideal seria ter uma coluna `data_matricula` na tabela `alunos`
$ultimas_matriculas = $conexao->query("
    SELECT nome_completo, data_nascimento 
    FROM alunos 
    ORDER BY id_aluno DESC 
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Contagem de matrículas por mês, usando a data de nascimento
$matriculas_por_mes = $conexao->query("
    SELECT DATE_FORMAT(data_nascimento, '%Y-%m') AS mes, COUNT(id_aluno) AS total
    FROM alunos
    GROUP BY mes
    ORDER BY mes ASC
")->fetch_all(MYSQLI_ASSOC);
?>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <div class="card-header">
             <h4><i class="fas fa-chart-line"></i> Evolução de Matrículas por Mês</h4>
        </div>
        <div class="card-body">
            <canvas id="matriculasChart"></canvas>
        </div>
    </div>
</div>

<div class="table-container" style="margin-top: 20px;">
    <div class="table-settings">
        <h3 class="section-title" style="margin: 0;">Últimos Alunos Matriculados</h3>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Nome do Aluno</th>
                <th>Data de Nascimento</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ultimas_matriculas)): ?>
                <tr><td colspan="2">Nenhum aluno matriculado encontrado.</td></tr>
            <?php else: ?>
                <?php foreach($ultimas_matriculas as $aluno): ?>
                <tr>
                    <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($aluno['data_nascimento'])); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('matriculasChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($matriculas_por_mes, 'mes')); ?>,
                datasets: [{
                    label: 'Novos Alunos',
                    data: <?php echo json_encode(array_column($matriculas_por_mes, 'total')); ?>,
                    borderColor: '#