<?php
// Inclui o header padrão e a conexão com o banco
require_once 'templates/header_responsavel.php';

// Pega o ID do aluno que está armazenado na sessão do responsável
$id_aluno_logado = $_SESSION['id_aluno'] ?? 0;

// Lógica para controlar o mês e ano do calendário
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');

// Cálculos para montar o calendário
$dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
$primeiro_dia_semana = date('w', strtotime("$ano-$mes-01"));
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese'); // Para nome do mês em português
$nome_mes = ucfirst(strftime('%B', mktime(0, 0, 0, $mes, 1, $ano)));

// Links de navegação para o mês anterior e próximo
$mes_anterior = $mes - 1;
$ano_anterior = $ano;
if ($mes_anterior == 0) {
    $mes_anterior = 12;
    $ano_anterior--;
}

$mes_seguinte = $mes + 1;
$ano_seguinte = $ano;
if ($mes_seguinte == 13) {
    $mes_seguinte = 1;
    $ano_seguinte++;
}

// Array para armazenar o status de cada dia
$dados_calendario = [];

if ($id_aluno_logado > 0) {
    // 1. Buscar registros de presença
    $sql_freq = "SELECT DAY(data) as dia, presenca FROM registro_presenca WHERE id_aluno = ? AND MONTH(data) = ? AND YEAR(data) = ?";
    $stmt_freq = $conexao->prepare($sql_freq);
    $stmt_freq->bind_param("iii", $id_aluno_logado, $mes, $ano);
    $stmt_freq->execute();
    $result_freq = $stmt_freq->get_result();
    while ($row = $result_freq->fetch_assoc()) {
        $dados_calendario[$row['dia']] = $row['presenca'];
    }

    // 2. Buscar atestados e sobrepor as faltas
    $sql_atestado = "SELECT data_inicio, data_fim FROM atestados WHERE id_aluno = ? AND ? BETWEEN YEAR(data_inicio) AND YEAR(data_fim)";
    $stmt_atestado = $conexao->prepare($sql_atestado);
    $stmt_atestado->bind_param("ii", $id_aluno_logado, $ano);
    $stmt_atestado->execute();
    $atestados = $stmt_atestado->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($atestados as $atestado) {
        $inicio = new DateTime($atestado['data_inicio']);
        $fim = new DateTime($atestado['data_fim']);
        $fim->modify('+1 day'); // Inclui o último dia no intervalo
        $intervalo = new DateInterval('P1D');
        $periodo = new DatePeriod($inicio, $intervalo, $fim);

        foreach ($periodo as $data) {
            if ($data->format('m') == $mes && $data->format('Y') == $ano) {
                $dia = (int)$data->format('d');
                if (isset($dados_calendario[$dia]) && $dados_calendario[$dia] == 'ausente') {
                    $dados_calendario[$dia] = 'Atestado';
                }
            }
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="section-title">Relatório de Frequência</h3>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="?mes=<?php echo $mes_anterior; ?>&ano=<?php echo $ano_anterior; ?>" class="btn btn-secondary">&lt; Anterior</a>
            <h4><?php echo $nome_mes . ' de ' . $ano; ?></h4>
            <a href="?mes=<?php echo $mes_seguinte; ?>&ano=<?php echo $ano_seguinte; ?>" class="btn btn-secondary">Próximo &gt;</a>
        </div>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    for ($i = 0; $i < $primeiro_dia_semana; $i++) {
                        echo '<td></td>';
                    }
                    for ($dia = 1; $dia <= $dias_no_mes; $dia++) {
                        if (($dia + $primeiro_dia_semana - 1) % 7 == 0 && $dia != 1) {
                            echo '</tr><tr>';
                        }
                        $status = $dados_calendario[$dia] ?? '';
                        $classe_css = '';
                        if ($status == 'presente') {
                            $classe_css = 'table-success';
                        } elseif ($status == 'ausente') {
                            $classe_css = 'table-danger';
                        } elseif ($status == 'Atestado') {
                            $classe_css = 'table-warning';
                        }
                        echo "<td class='$classe_css'>$dia</td>";
                    }
                    $posicao_final = ($primeiro_dia_semana + $dias_no_mes) % 7;
                    if ($posicao_final != 0) {
                        for ($i = $posicao_final; $i < 7; $i++) {
                            echo '<td></td>';
                        }
                    }
                    ?>
                </tr>
            </tbody>
        </table>
        <div class="mt-3">
            <strong>Legenda:</strong>
            <span class="badge bg-success">Presente</span>
            <span class="badge bg-danger">Falta</span>
            <span class="badge bg-warning text-dark">Falta Justificada</span>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer_responsavel.php';
?>