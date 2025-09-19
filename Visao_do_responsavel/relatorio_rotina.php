<?php
// Inclui o header padrão e a conexão
require_once 'templates/header_responsavel.php';

// Pega o ID do aluno da sessão
$id_aluno_logado = $_SESSION['id_aluno'] ?? 0;

// Define a data a ser consultada (hoje por padrão, ou a data do GET)
$data_selecionada = $_GET['data'] ?? date('Y-m-d');

// Busca os registros diários para o aluno e a data selecionada
$registros = [];
if ($id_aluno_logado > 0) {
    $sql = "
        SELECT rd.*, u.nome_completo as nome_bercarista
        FROM registros_diarios rd
        LEFT JOIN usuarios u ON rd.id_bercarista = u.id_usuario
        WHERE rd.id_aluno = :id_aluno AND rd.data_registro = :data_registro
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_aluno', $id_aluno_logado);
    $stmt->bindParam(':data_registro', $data_selecionada);
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Agrupa os registros por categoria para facilitar a exibição
$rotina_dia = [
    'alimentacao' => [],
    'sono' => [],
    'higiene' => [],
    'observacoes' => ''
];

foreach ($registros as $registro) {
    if (!empty($registro['horario_alimentacao'])) {
        $rotina_dia['alimentacao'][] = [
            'horario' => $registro['horario_alimentacao'],
            'refeicao' => $registro['refeicao'],
            'aceitacao' => $registro['aceitacao_refeicao']
        ];
    }
    if (!empty($registro['horario_inicio_sono'])) {
        $rotina_dia['sono'][] = [
            'inicio' => $registro['horario_inicio_sono'],
            'fim' => $registro['horario_fim_sono'],
            'qualidade' => $registro['qualidade_sono']
        ];
    }
    if (!empty($registro['horario_higiene'])) {
        $rotina_dia['higiene'][] = [
            'horario' => $registro['horario_higiene'],
            'tipo' => $registro['tipo_higiene']
        ];
    }
    if (!empty($registro['observacoes'])) {
        $rotina_dia['observacoes'] = $registro['observacoes'];
    }
}
?>

<!-- Estilos para os cards do relatório -->
<style>
    .rotina-card {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .rotina-card h4 {
        color: #0056b3;
        margin-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    .rotina-item {
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    .rotina-item:last-child {
        border-bottom: none;
    }
    .rotina-item strong {
        color: #333;
    }
</style>

<div class="container-responsavel">
    <h3><i class="fas fa-clipboard-list"></i> Relatório da Rotina Diária</h3>

    <!-- Formulário para selecionar a data -->
    <form method="GET" action="" class="mb-4">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label for="data" class="form-label"><strong>Selecione o dia:</strong></label>
                <input type="date" id="data" name="data" class="form-control" value="<?php echo htmlspecialchars($data_selecionada); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Consultar</button>
            </div>
        </div>
    </form>

    <?php if (empty($registros) && $_SERVER['REQUEST_METHOD'] !== 'GET'): ?>
        <div class="alert alert-info">Nenhum registro de rotina encontrado para esta data.</div>
    <?php else: ?>
        <!-- Card de Alimentação -->
        <div class="rotina-card">
            <h4><i class="fas fa-utensils"></i> Alimentação</h4>
            <?php if (!empty($rotina_dia['alimentacao'])): ?>
                <?php foreach ($rotina_dia['alimentacao'] as $item): ?>
                    <div class="rotina-item">
                        <strong>Horário:</strong> <?php echo htmlspecialchars($item['horario']); ?><br>
                        <strong>Refeição:</strong> <?php echo htmlspecialchars($item['refeicao']); ?><br>
                        <strong>Aceitação:</strong> <?php echo htmlspecialchars($item['aceitacao']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum registro de alimentação para este dia.</p>
            <?php endif; ?>
        </div>

        <!-- Card de Sono -->
        <div class="rotina-card">
            <h4><i class="fas fa-bed"></i> Sono</h4>
            <?php if (!empty($rotina_dia['sono'])): ?>
                <?php foreach ($rotina_dia['sono'] as $item): ?>
                    <div class="rotina-item">
                        <strong>Início:</strong> <?php echo htmlspecialchars($item['inicio']); ?> | <strong>Fim:</strong> <?php echo htmlspecialchars($item['fim']); ?><br>
                        <strong>Qualidade do Sono:</strong> <?php echo htmlspecialchars($item['qualidade']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum registro de sono para este dia.</p>
            <?php endif; ?>
        </div>

        <!-- Card de Higiene -->
        <div class="rotina-card">
            <h4><i class="fas fa-bath"></i> Higiene</h4>
            <?php if (!empty($rotina_dia['higiene'])): ?>
                <?php foreach ($rotina_dia['higiene'] as $item): ?>
                    <div class="rotina-item">
                        <strong>Horário:</strong> <?php echo htmlspecialchars($item['horario']); ?><br>
                        <strong>Tipo:</strong> <?php echo htmlspecialchars($item['tipo']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum registro de higiene para este dia.</p>
            <?php endif; ?>
        </div>

        <!-- Card de Observações -->
        <div class="rotina-card">
            <h4><i class="fas fa-comment-alt"></i> Observações Gerais</h4>
            <p><?php echo !empty($rotina_dia['observacoes']) ? htmlspecialchars($rotina_dia['observacoes']) : 'Nenhuma observação geral registrada para este dia.'; ?></p>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'templates/footer_responsavel.php';
?>
