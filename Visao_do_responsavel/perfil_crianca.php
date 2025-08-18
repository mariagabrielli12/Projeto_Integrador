<?php
$page_title = 'Perfil da Criança';
$page_icon = 'fas fa-child';
require_once '../templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS ---
// Busca os dados do aluno associado ao responsável logado
$stmt = $conexao->prepare(
    "SELECT a.*, t.nome AS nome_turma, t.periodo 
     FROM alunos a
     LEFT JOIN turmas t ON a.turma_id = t.id
     WHERE a.id = ?"
);
$stmt->bind_param("i", $aluno_id_associado);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();
$stmt->close();

// Busca os dados do contato de emergência (o próprio responsável)
$stmt_resp = $conexao->prepare("SELECT nome, telefone FROM usuarios WHERE id = ?");
$stmt_resp->bind_param("i", $responsavel_logado_id);
$stmt_resp->execute();
$responsavel = $stmt_resp->get_result()->fetch_assoc();
$stmt_resp->close();

if (!$aluno) {
    echo "Erro: Não foi possível encontrar os dados da criança.";
    exit;
}
// --- FIM DA LÓGICA ---
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Informações de <?php echo htmlspecialchars($aluno['nome_completo']); ?></h3></div>
    <div class="card-body">
        <h3 class="section-title">Informações Pessoais</h3>
        <div class="form-row">
            <div class="form-group"><label>Nome</label><input type="text" value="<?php echo htmlspecialchars($aluno['nome_completo']); ?>" readonly></div>
            <div class="form-group"><label>Data de Nascimento</label><input type="text" value="<?php echo date('d/m/Y', strtotime($aluno['data_nascimento'])); ?>" readonly></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Turma</label><input type="text" value="<?php echo htmlspecialchars($aluno['nome_turma'] ?? 'Não matriculado'); ?>" readonly></div>
            <div class="form-group"><label>Turno</label><input type="text" value="<?php echo htmlspecialchars($aluno['periodo'] ?? 'N/D'); ?>" readonly></div>
        </div>
        
        <h3 class="section-title">Informações de Saúde</h3>
        <div class="form-row">
            <div class="form-group"><label>Alergias</label><input type="text" value="<?php echo htmlspecialchars($aluno['alergias'] ?: 'Nenhuma registrada'); ?>" readonly></div>
            <div class="form-group"><label>Medicamentos de Uso Contínuo</label><input type="text" value="<?php echo htmlspecialchars($aluno['medicamentos_uso_continuo'] ?: 'Nenhum'); ?>" readonly></div>
        </div>
        
        <h3 class="section-title">Contato de Emergência</h3>
        <div class="form-row">
            <div class="form-group"><label>Nome</label><input type="text" value="<?php echo htmlspecialchars($responsavel['nome'] ?? 'Não informado'); ?>" readonly></div>
            <div class="form-group"><label>Telefone</label><input type="text" value="<?php echo htmlspecialchars($responsavel['telefone'] ?? 'Não informado'); ?>" readonly></div>
        </div>
    </div>
</div>

<?php
require_once '../templates/footer_responsavel.php';
?>