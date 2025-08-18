<?php
// Define as variáveis da página
$page_title = 'Detalhes do Relatório';
$page_icon = 'fas fa-file-alt';

// Inclui o cabeçalho do responsável
require_once '../templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS ---
// Pega o ID do relatório da URL, garantindo que seja um número inteiro
$relatorio_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Se não houver ID, exibe um erro e para a execução
if ($relatorio_id === 0) {
    echo "<p class='alert error'>Erro: ID do relatório não fornecido.</p>";
    require_once '../templates/footer_responsavel.php';
    exit;
}

// Prepara a consulta para buscar o relatório específico do aluno logado
$query = "
    SELECT 
        do.*, 
        u.nome as nome_professor,
        a.nome_completo as nome_aluno
    FROM desenvolvimento_observacoes do
    JOIN usuarios u ON do.professor_id = u.id
    JOIN alunos a ON do.aluno_id = a.id
    WHERE do.id = ? AND do.aluno_id = ?
";
$stmt = $conexao->prepare($query);
// Usa o ID do relatório da URL e o ID do aluno da sessão (simulado no header)
$stmt->bind_param("ii", $relatorio_id, $aluno_id_associado);
$stmt->execute();
$relatorio = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Se a consulta não retornar nada, significa que o relatório não existe ou não pertence a esta criança
if (!$relatorio) {
    echo "<p class='alert error'>Erro: Relatório não encontrado ou acesso não permitido.</p>";
    require_once '../templates/footer_responsavel.php';
    exit;
}
// --- FIM DA LÓGICA ---
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-child"></i>
        <h3 class="section-title">Relatório de Desenvolvimento - <?php echo htmlspecialchars($relatorio['nome_aluno']); ?></h3>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label>Período (Data da Observação)</label>
                <input type="text" value="<?php echo date('d/m/Y', strtotime($relatorio['data_observacao'])); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Autor</label>
                <input type="text" value="<?php echo htmlspecialchars($relatorio['nome_professor']); ?>" readonly>
            </div>
        </div>

        <div class="report-detail-section">
            <h4><i class="fas fa-running"></i> Área de Desenvolvimento: <?php echo htmlspecialchars($relatorio['area_desenvolvimento']); ?></h4>
            <div class="report-content">
                <p><strong>Habilidade Observada:</strong> <?php echo htmlspecialchars($relatorio['habilidade_observada']); ?></p>
            </div>
        </div>

        <div class="report-detail-section">
            <h4><i class="fas fa-pencil-alt"></i> Descrição e Análise do Professor</h4>
            <div class="report-content">
                <p><?php echo nl2br(htmlspecialchars($relatorio['descricao'])); ?></p>
            </div>
        </div>

        <div class="recommendations">
            <h5><i class="fas fa-lightbulb"></i> Sugestões para o Desenvolvimento em Casa</h5>
            <ul>
                <li>Incentivar a leitura de livros infantis.</li>
                <li>Propor brincadeiras que envolvam contagem.</li>
            </ul>
        </div>

        <div class="teacher-signature">
            <span>Atenciosamente,</span>
            <span><?php echo htmlspecialchars($relatorio['nome_professor']); ?></span>
        </div>

        <div class="form-actions" style="margin-top: 30px;">
            <a href="relatorios_responsavel.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar para Relatórios</a>
            <button class="btn btn-primary" onclick="window.print();"><i class="fas fa-print"></i> Imprimir Relatório</button>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé
require_once '../templates/footer_responsavel.php';
?>