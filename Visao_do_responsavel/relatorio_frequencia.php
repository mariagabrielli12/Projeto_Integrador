<?php
$page_title = 'Relatório de Frequência';
$page_icon = 'fas fa-calendar-check';
require_once 'templates/header_responsavel.php';

// O header_responsavel.php já busca e define a variável $id_aluno_logado a partir da sessão

// Lógica para controlar o mês e ano do calendário
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');

// --- CORREÇÃO AQUI: Lógica para obter o nome do mês ---
// Cria um objeto DateTime para o primeiro dia do mês/ano selecionado
$dateObject = DateTime::createFromFormat('!m-Y', "$mes-$ano");
// Usa a classe IntlDateFormatter para obter o nome do mês em português (mais moderno e confiável)
$formatter = new IntlDateFormatter('pt_BR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'MMMM');
$nome_mes = ucfirst($formatter->format($dateObject));


// Cálculos para montar o calendário
$dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
$primeiro_dia_semana = date('w', strtotime("$ano-$mes-01"));

// Links de navegação para o mês anterior e próximo
$mes_anterior = $mes - 1; $ano_anterior = $ano;
if ($mes_anterior == 0) { $mes_anterior = 12; $ano_anterior--; }

$mes_seguinte = $mes + 1; $ano_seguinte = $ano;
if ($mes_seguinte == 13) { $mes_seguinte = 1; $ano_seguinte++; }

// Array para armazenar o status de cada dia
$dados_calendario = [];

if ($id_aluno_logado > 0) {
    // 1. Buscar registros de presença do mês
    $stmt_freq = $conexao->prepare("SELECT DAY(data) as dia, presenca FROM registro_presenca WHERE id_aluno = ? AND MONTH(data) = ? AND YEAR(data) = ?");
    $stmt_freq->bind_param("iii", $id_aluno_logado, $mes, $ano);
    $stmt_freq->execute();
    $result_freq = $stmt_freq->get_result();
    while ($row = $result_freq->fetch_assoc()) {
        $dados_calendario[$row['dia']] = $row['presenca'];
    }
    $stmt_freq->close();

    // 2. Buscar atestados que se sobrepõem ao mês atual
    $primeiro_dia_mes = "$ano-$mes-01";
    $ultimo_dia_mes = "$ano-$mes-$dias_no_mes";
    
    $stmt_atestado = $conexao->prepare("SELECT data_inicio, data_fim FROM atestados WHERE id_aluno = ? AND data_inicio <= ? AND data_fim >= ?");
    $stmt_atestado->bind_param("iss", $id_aluno_logado, $ultimo_dia_mes, $primeiro_dia_mes);
    $stmt_atestado->execute();
    $atestados = $stmt_atestado->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_atestado->close();

    foreach ($atestados as $atestado) {
        $inicio = new DateTime($atestado['data_inicio']);
        $fim = new DateTime($atestado['data_fim']);
        $fim->modify('+1 day');
        $intervalo = new DateInterval('P1D');
        $periodo = new DatePeriod($inicio, $intervalo, $fim);

        foreach ($periodo as $data) {
            if ($data->format('m') == $mes && $data->format('Y') == $ano) {
                $dia = (int)$data->format('d');
                $dados_calendario[$dia] = 'Atestado';
            }
        }
    }
}
?>
<style>
    .calendario-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .calendario-nav h4 { margin: 0; font-size: 1.4em; color: var(--primary); }
    .table-calendario { width: 100%; border-collapse: collapse; }
    .table-calendario th, .table-calendario td { border: 1px solid #ddd; padding: 10px; text-align: center; height: 90px; vertical-align: middle; }
    .table-calendario th { background-color: #f8f9fa; font-weight: 600; }
    .dia-presente { background-color: #d4edda; color: #155724; font-weight: bold; }
    .dia-ausente { background-color: #f8d7da; color: #721c24; font-weight: bold; }
    .dia-atestado { background-color: #fff3cd; color: #856404; font-weight: bold; }
    .legenda { margin-top: 20px; display: flex; gap: 15px; flex-wrap: wrap; }
    .legenda-item { display: flex; align-items: center; gap: 5px; font-size: 0.9em; }
    .legenda-cor { width: 15px; height: 15px; border-radius: 4px; }
</style>

<div class="card">
    <div class="card-header">
        <h3 class="section-title">Relatório de Frequência</h3>
    </div>
    <div class="card-body">
        <div class="calendario-nav">
            <a href="?mes=<?php echo $mes_anterior; ?>&ano=<?php echo $ano_anterior; ?>" class="btn btn-secondary">&lt; Mês Anterior</a>
            <h4><?php echo $nome_mes . ' de ' . $ano; ?></h4>
            <a href="?mes=<?php echo $mes_seguinte; ?>&ano=<?php echo $ano_seguinte; ?>" class="btn btn-secondary">Próximo Mês &gt;</a>
        </div>
        <table class="table-calendario">
            <thead>
                <tr>
                    <th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    // Células vazias para o início do mês
                    for ($i = 0; $i < $primeiro_dia_semana; $i++) { echo '<td></td>'; }

                    // Dias do mês
                    for ($dia = 1; $dia <= $dias_no_mes; $dia++) {
                        if (($dia + $primeiro_dia_semana - 1) % 7 == 0 && $dia != 1) { echo '</tr><tr>'; }
                        
                        $status = $dados_calendario[$dia] ?? '';
                        $classe_css = '';
                        if ($status == 'presente') { $classe_css = 'dia-presente'; } 
                        elseif ($status == 'ausente') { $classe_css = 'dia-ausente'; } 
                        elseif ($status == 'Atestado') { $classe_css = 'dia-atestado'; }
                        
                        echo "<td class='$classe_css'>$dia</td>";
                    }

                    // Células vazias para o final do mês
                    $posicao_final = ($primeiro_dia_semana + $dias_no_mes) % 7;
                    if ($posicao_final != 0) {
                        for ($i = $posicao_final; $i < 7; $i++) { echo '<td></td>'; }
                    }
                    ?>
                </tr>
            </tbody>
        </table>
        <div class="legenda">
            <strong>Legenda:</strong>
            <div class="legenda-item"><div class="legenda-cor dia-presente"></div> Presente</div>
            <div class="legenda-item"><div class="legenda-cor dia-ausente"></div> Falta</div>
            <div class="legenda-item"><div class="legenda-cor dia-atestado"></div> Falta Justificada</div>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer_responsavel.php';
?>