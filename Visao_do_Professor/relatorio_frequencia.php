<?php
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

$id_professor = $_SESSION['id_usuario'];

// Lógica para buscar as turmas do professor
$sql_turmas = "SELECT * FROM turmas WHERE id_professor = ?";
$stmt_turmas = $conexao->prepare($sql_turmas);
$stmt_turmas->bind_param('i', $id_professor);
$stmt_turmas->execute();
$turmas = $stmt_turmas->get_result()->fetch_all(MYSQLI_ASSOC);

$alunos_frequencia = [];
$dias_no_mes = 0;
$mes_selecionado = '';
$ano_selecionado = '';
$turma_selecionada_nome = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['turma_id']) && isset($_POST['mes'])) {
    $turma_id = $_POST['turma_id'];
    list($ano_selecionado, $mes_selecionado) = explode('-', $_POST['mes']);
    $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes_selecionado, $ano_selecionado);

    // Buscar nome da turma selecionada
    $sql_nome_turma = "SELECT nome_turma FROM turmas WHERE id_turma = ?";
    $stmt_nome_turma = $conexao->prepare($sql_nome_turma);
    $stmt_nome_turma->bind_param('i', $turma_id);
    $stmt_nome_turma->execute();
    $turma_info = $stmt_nome_turma->get_result()->fetch_assoc();
    $turma_selecionada_nome = $turma_info ? $turma_info['nome_turma'] : '';

    // Buscar alunos da turma
    $sql_alunos = "SELECT id_aluno, nome_completo FROM alunos WHERE id_turma = ? ORDER BY nome_completo ASC";
    $stmt_alunos = $conexao->prepare($sql_alunos);
    $stmt_alunos->bind_param('i', $turma_id);
    $stmt_alunos->execute();
    $alunos = $stmt_alunos->get_result()->fetch_all(MYSQLI_ASSOC);

    // Para cada aluno, buscar seus registros de frequência no mês
    foreach ($alunos as $aluno) {
        $id_aluno = $aluno['id_aluno'];
        $frequencia_aluno = [];

        for ($dia = 1; $dia <= $dias_no_mes; $dia++) {
            $data_presenca = sprintf('%s-%s-%02d', $ano_selecionado, $mes_selecionado, $dia);

            $sql_freq = "SELECT presenca FROM registro_presenca WHERE id_aluno = ? AND data = ?";
            $stmt_freq = $conexao->prepare($sql_freq);
            $stmt_freq->bind_param('is', $id_aluno, $data_presenca);
            $stmt_freq->execute();
            $registro = $stmt_freq->get_result()->fetch_assoc();

            $frequencia_aluno[$dia] = $registro ? $registro['presenca'] : '-'; // '-' para dias sem registro
        }

        $alunos_frequencia[] = [
            'nome' => $aluno['nome_completo'],
            'frequencia' => $frequencia_aluno
        ];
    }
}
?>

<div class="container mt-5">
    <h2>Relatório de Frequência da Turma</h2>
    <p>Selecione a turma e o mês para gerar o relatório de frequência.</p>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Filtros</h5>
            <form action="relatorio_frequencia.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="turma_id" class="form-label">Turma</label>
                        <select name="turma_id" id="turma_id" class="form-select" required>
                            <option value="">Selecione uma Turma</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?php echo htmlspecialchars($turma['id_turma']); ?>">
                                    <?php echo htmlspecialchars($turma['nome_turma']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="mes" class="form-label">Mês e Ano</label>
                        <input type="month" name="mes" id="mes" class="form-control" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end mb-3">
                        <button type="submit" class="btn btn-primary w-100">Gerar Relatório</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($alunos_frequencia)): ?>
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Frequência - <?php echo htmlspecialchars($turma_selecionada_nome) . " - " . date("F/Y", mktime(0, 0, 0, $mes_selecionado, 1, $ano_selecionado)); ?></h5>
                <button onclick="window.print()" class="btn btn-secondary btn-sm">Imprimir</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 200px;">Aluno</th>
                                <?php for ($dia = 1; $dia <= $dias_no_mes; $dia++): ?>
                                    <th><?php echo $dia; ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alunos_frequencia as $aluno): ?>
                                <tr>
                                    <td class="text-start"><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                    <?php foreach ($aluno['frequencia'] as $status): ?>
                                        <td>
                                            <?php
                                                if ($status == 'presente') {
                                                    echo '<span class="text-success">P</span>';
                                                } elseif ($status == 'ausente') {
                                                    echo '<span class="text-danger">A</span>';
                                                } else {
                                                    echo $status;
                                                }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                 <div class="mt-3">
                    <strong>Legenda:</strong>
                    <span class="text-success ms-2">P</span> = Presente
                    <span class="text-danger ms-2">A</span> = Ausente
                    <span class="ms-2">-</span> = Sem Registro
                </div>
            </div>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <div class="alert alert-info">Nenhum dado de frequência encontrado para os filtros selecionados.</div>
    <?php endif; ?>

</div>

<?php
include_once 'templates/footer_professor.php';
?>