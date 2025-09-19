<?php
$page_title = 'Perfil da Criança';
$page_icon = 'fas fa-child';
require_once 'templates/header_responsavel.php';

// --- BUSCA OS DADOS DO ALUNO (ATUALIZADO) ---
$aluno = null;
$stmt = $conexao->prepare(
    "SELECT a.*, t.nome_turma 
     FROM alunos a
     JOIN alunos_responsaveis ar ON a.id_aluno = ar.id_aluno
     LEFT JOIN turmas t ON a.id_turma = t.id_turma
     WHERE ar.id_responsavel = ? 
     LIMIT 1"
);
$stmt->bind_param("i", $id_responsavel_logado);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $aluno = $result->fetch_assoc();
}
$stmt->close();
?>

<?php if ($aluno): ?>
    <div class="card">
        <div class="card-header"><h3 class="section-title"><?php echo htmlspecialchars($aluno['nome_completo']); ?></h3></div>
        <div class="card-body">
            <h3 class="section-title">Informações Pessoais</h3>
            <div class="form-row">
                <div class="form-group"><label>Nome Completo</label><input type="text" value="<?php echo htmlspecialchars($aluno['nome_completo']); ?>" readonly></div>
                <div class="form-group"><label>Data de Nascimento</label><input type="text" value="<?php echo date("d/m/Y", strtotime($aluno['data_nascimento'])); ?>" readonly></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Turma</label><input type="text" value="<?php echo htmlspecialchars($aluno['nome_turma'] ?? 'Não matriculado'); ?>" readonly></div>
                <div class="form-group"><label>Género</label><input type="text" value="<?php echo htmlspecialchars($aluno['genero'] ?? 'Não informado'); ?>" readonly></div>
            </div>

            <h3 class="section-title">Endereço</h3>
            <div class="form-group"><label>Endereço Completo</label><input type="text" value="<?php echo htmlspecialchars($aluno['logradouro'] . ', ' . $aluno['numero'] . ' - ' . $aluno['bairro']); ?>" readonly></div>
            
            <h3 class="section-title">Informações de Contato</h3>
            <div class="form-row">
                <div class="form-group"><label>Telefone do Responsável</label><input type="text" value="<?php echo htmlspecialchars($aluno['contato_responsavel']); ?>" readonly></div>
                <div class="form-group"><label>E-mail do Responsável</label><input type="text" value="<?php echo htmlspecialchars($aluno['email_responsavel']); ?>" readonly></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card"><div class="card-body"><p>Nenhuma criança está associada a este perfil de responsável no momento.</p></div></div>
<?php endif; ?>

<?php require_once 'templates/footer_responsavel.php'; ?>