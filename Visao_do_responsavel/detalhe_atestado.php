<?php
$page_title = 'Detalhes do Atestado';
$page_icon = 'fas fa-file-medical-alt';
require_once 'templates/header_responsavel.php';

// --- LÓGICA DA BASE DE DADOS ---
$atestado_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($atestado_id === 0) {
    echo "<p class='alert error'>Erro: ID do atestado não fornecido.</p>";
    require_once 'templates/footer_responsavel.php';
    exit;
}

// Prepara a consulta para buscar o atestado, garantindo que ele pertence ao filho do responsável logado
$query = "
    SELECT 
        at.*, 
        a.nome_completo as nome_aluno
    FROM atestados at
    JOIN alunos a ON at.id_aluno = a.id_aluno
    JOIN alunos_responsaveis ar ON a.id_aluno = ar.id_aluno
    WHERE at.id_atestado = ? AND ar.id_responsavel = ?
";
$stmt = $conexao->prepare($query);
$stmt->bind_param("ii", $atestado_id, $id_responsavel_logado);
$stmt->execute();
$atestado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$atestado) {
    echo "<p class='alert error'>Erro: Atestado não encontrado ou acesso não permitido.</p>";
    require_once 'templates/footer_responsavel.php';
    exit;
}
?>

<div class="card">
    <div class="card-header"><i class="fas fa-notes-medical"></i><h3 class="section-title">Detalhes do Atestado de <?php echo htmlspecialchars($atestado['nome_aluno']); ?></h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label>Início do Afastamento</label>
                <input type="text" value="<?php echo date('d/m/Y', strtotime($atestado['data_inicio'])); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Fim do Afastamento</label>
                <input type="text" value="<?php echo date('d/m/Y', strtotime($atestado['data_fim'])); ?>" readonly>
            </div>
        </div>

        <div class="form-group">
            <label>Motivo / Observações</label>
            <textarea rows="4" readonly><?php echo htmlspecialchars($atestado['motivo']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Anexo</label>
            <p>
                <a href="<?php echo htmlspecialchars($atestado['caminho_anexo']); ?>" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-eye"></i> Ver Anexo
                </a>
                <a href="<?php echo htmlspecialchars($atestado['caminho_anexo']); ?>" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Baixar Anexo
                </a>
            </p>
        </div>

        <div class="form-actions" style="margin-top: 30px;">
            <a href="atestados_responsavel.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar para a Lista</a>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer_responsavel.php';
?>