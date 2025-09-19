<?php
$page_title = 'Troca de Turma';
$page_icon = 'fas fa-exchange-alt';
require_once '../templates/header_secretario.php';

// --- PROCESSAMENTO DO FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aluno = $_POST['aluno_id'];
    $nova_turma_id = $_POST['nova_turma_id'];
    
    if (empty($id_aluno) || empty($nova_turma_id)) {
        $erro = "Por favor, selecione o aluno e a nova turma.";
    } else {
        $stmt = $conexao->prepare("UPDATE alunos SET id_turma = ? WHERE id_aluno = ?");
        $stmt->bind_param("ii", $nova_turma_id, $id_aluno);

        if ($stmt->execute()) {
            header("Location: Troca_Turma.php?sucesso=A troca de turma foi realizada com sucesso!");
            exit();
        } else {
            $erro = "Ocorreu um erro ao tentar realizar a troca. Tente novamente.";
        }
        $stmt->close();
    }
}

// --- LÓGICA PARA POPULAR OS DROPDOWNS ---
$alunos_sql = "SELECT a.id_aluno, a.nome_completo, t.nome_turma 
               FROM alunos a 
               LEFT JOIN turmas t ON a.id_turma = t.id_turma 
               ORDER BY a.nome_completo";
$alunos_result = $conexao->query($alunos_sql);

$turmas_result = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma");
?>

<div class="form-container">
    <?php if(isset($_GET['sucesso'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>
    <?php if(isset($erro)): ?>
        <div class="alert error"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="section-title">Dados para Troca de Turma</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="Troca_Turma.php">
                <div class="form-row">
                    <div class="form-group">
                        <label>Selecione o Aluno*</label>
                        <select name="aluno_id" required onchange="atualizarTurmaAtual(this)">
                            <option value="">-- Selecione um aluno --</option>
                            <?php 
                            if ($alunos_result->num_rows > 0) {
                                while($aluno = $alunos_result->fetch_assoc()): ?>
                                    <option value="<?php echo $aluno['id_aluno']; ?>" data-turma-atual="<?php echo htmlspecialchars($aluno['nome_turma'] ?? 'Sem turma'); ?>">
                                        <?php echo htmlspecialchars($aluno['nome_completo']); ?>
                                    </option>
                                <?php endwhile;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Turma Atual</label>
                        <input type="text" id="current-class" readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Selecione a Nova Turma*</label>
                        <select name="nova_turma_id" required>
                            <option value="">-- Selecione a nova turma --</option>
                            <?php 
                             if ($turmas_result->num_rows > 0) {
                                $turmas_result->data_seek(0);
                                while($turma = $turmas_result->fetch_assoc()): ?>
                                    <option value="<?php echo $turma['id_turma']; ?>">
                                        <?php echo htmlspecialchars($turma['nome_turma']); ?>
                                    </option>
                                <?php endwhile;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-exchange-alt"></i> Confirmar Troca</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function atualizarTurmaAtual(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const turmaAtual = selectedOption.getAttribute('data-turma-atual');
        document.getElementById('current-class').value = turmaAtual;
    }
</script>

<?php 
$conexao->close();
require_once '../templates/footer_secretario.php'; 
?>