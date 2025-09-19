<?php
// Inclui o header e a conexão
require_once('../templates/header_secretario.php');

// --- LÓGICA DO BANCO DE DADOS ---

// Consulta para buscar a contagem de alunos e a capacidade de cada turma
$sql = "
    SELECT 
        t.nome_turma,
        s.capacidade,
        COUNT(a.id_aluno) AS alunos_matriculados
    FROM turmas t
    LEFT JOIN salas s ON t.id_sala = s.id_sala
    LEFT JOIN alunos a ON t.id_turma = a.id_turma
    GROUP BY t.id_turma, t.nome_turma, s.capacidade
    ORDER BY t.nome_turma;
";

$stmt = $conexao->prepare($sql);
$stmt->execute();
$dados_turmas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Prepara os dados para o gráfico
$labels = [];
$dados_matriculados = [];
$dados_vagas = [];
$cores_matriculados = [];
$cores_vagas = [];

foreach ($dados_turmas as $turma) {
    $labels[] = $turma['nome_turma'];
    $matriculados = (int)$turma['alunos_matriculados'];
    $capacidade = (int)$turma['capacidade'];
    $vagas = $capacidade - $matriculados;

    $dados_matriculados[] = $matriculados;
    $dados_vagas[] = $vagas > 0 ? $vagas : 0; // Garante que não haja vagas negativas

    // Define cores com base na lotação
    if ($matriculados >= $capacidade) {
        $cores_matriculados[] = 'rgba(211, 47, 47, 0.7)'; // Vermelho (lotado)
    } elseif ($capacidade > 0 && ($matriculados / $capacidade) >= 0.8) {
        $cores_matriculados[] = 'rgba(255, 167, 38, 0.7)'; // Laranja (quase lotado)
    } else {
        $cores_matriculados[] = 'rgba(67, 160, 71, 0.7)'; // Verde (vagas disponíveis)
    }
    $cores_vagas[] = 'rgba(224, 224, 224, 0.7)'; // Cinza para vagas
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .chart-container {
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .table-vagas {
        margin-top: 30px;
    }
</style>

<div class="container-secretario">
    <h3><i class="fas fa-chart-pie"></i> Relatório de Vagas por Turma</h3>
    <p>Visualize a ocupação atual de cada turma da creche.</p>

    <div class="chart-container">
        <canvas id="graficoVagas"></canvas>
    </div>

    <div class="table-vagas">
        <h4>Detalhes das Turmas</h4>
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Turma</th>
                    <th>Capacidade Total</th>
                    <th>Alunos Matriculados</th>
                    <th>Vagas Disponíveis</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados_turmas as $turma): ?>
                    <?php
                        $matriculados = (int)$turma['alunos_matriculados'];
                        $capacidade = (int)$turma['capacidade'];
                        $vagas = $capacidade - $matriculados;
                        $status = $vagas > 0 ? "<span class='text-success'>Disponível</span>" : "<span class='text-danger'>Lotado</span>";
                        if ($capacidade > 0 && $vagas > 0 && ($matriculados / $capacidade) >= 0.8) {
                            $status = "<span class='text-warning'>Quase Lotado</span>";
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($turma['nome_turma']); ?></td>
                        <td><?php echo $capacidade; ?></td>
                        <td><?php echo $matriculados; ?></td>
                        <td><?php echo ($vagas > 0 ? $vagas : 0); ?></td>
                        <td><?php echo $status; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('graficoVagas').getContext('2d');
    const graficoVagas = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Alunos Matriculados',
                data: <?php echo json_encode($dados_matriculados); ?>,
                backgroundColor: <?php echo json_encode($cores_matriculados); ?>,
                borderColor: <?php echo json_encode(array_map(function($color) { return str_replace('0.7', '1', $color); }, $cores_matriculados)); ?>,
                borderWidth: 1
            }, {
                label: 'Vagas Disponíveis',
                data: <?php echo json_encode($dados_vagas); ?>,
                backgroundColor: <?php echo json_encode($cores_vagas); ?>,
                borderColor: 'rgba(158, 158, 158, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Ocupação de Vagas por Turma',
                    font: { size: 18 }
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    stacked: true, // Empilha as barras
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Número de Alunos'
                    }
                }
            }
        }
    });
});
</script>


<?php
// Inclui o footer padrão
require_once('../templates/footer_secretario.php');
?>