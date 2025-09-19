<?php
$page_title = 'Detalhes do Relatório';
$page_icon = 'fas fa-file-alt';
require_once 'templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS (ATUALIZADO) ---
$relatorio_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($relatorio_id === 0) {
    echo "<p class='alert error'>Erro: ID do relatório não fornecido.</p>";
    require_once 'templates/footer_responsavel.php';
    exit;
}

// Prepara a consulta para buscar o relatório, garantindo que ele pertence ao filho do responsável logado
$query = "
    SELECT 
        do.*, 
        u.nome_completo as nome_professor,
        a.nome_completo as nome_aluno
    FROM desenvolvimento_observacoes do
    JOIN usuarios u ON do.id_professor = u.id_usuario
    JOIN alunos a ON do.id_aluno = a.id_aluno
    JOIN alunos_responsaveis ar ON a.id_aluno = ar.id_aluno
    WHERE do.id_observacao = ? AND ar.id_responsavel = ?
";
$stmt = $conexao->prepare($query);
$stmt->bind_param("ii", $relatorio_id, $id_responsavel_logado);
$stmt->execute();
$relatorio = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$relatorio) {
    echo "<p class='alert error'>Erro: Relatório não encontrado ou acesso não permitido.</p>";
    require_once 'templates/footer_responsavel.php';
    exit;
}
?>

<div class="card">
    <div class="card-header"><i class="fas fa-child"></i><h3 class="section-title">Relatório de Desenvolvimento - <?php echo htmlspecialchars($relatorio['nome_aluno']); ?></h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group"><label>Data da Observação</label><input type="text" value="<?php echo date('d/m/Y', strtotime($relatorio['data_observacao'])); ?>" readonly></div>
            <div class="form-group"><label>Professor(a)</label><input type="text" value="<?php echo htmlspecialchars($relatorio['nome_professor']); ?>" readonly></div>
        </div>

        <div class="report-detail-section">
            <h4><i class="fas fa-puzzle-piece"></i> Área de Desenvolvimento: <?php echo htmlspecialchars($relatorio['area_desenvolvimento']); ?></h4>
            <div class="report-content"><p><strong>Habilidade Observada:</strong> <?php echo htmlspecialchars($relatorio['habilidade_observada']); ?></p></div>
        </div>

        <div class="report-detail-section">
            <h4><i class="fas fa-pencil-alt"></i> Descrição e Análise do Professor</h4>
            <div class="report-content"><p><?php echo nl2br(htmlspecialchars($relatorio['descricao'])); ?></p></div>
        </div>

        <div class="form-actions" style="margin-top: 30px;">
            <a href="relatorios_responsavel.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar para Relatórios</a>
            <button class="btn btn-primary" onclick="window.print();"><i class="fas fa-print"></i> Imprimir Relatório</button>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer_responsavel.php';
?>