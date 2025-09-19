<?php
// Inclui o header e a conexão
require_once('../templates/header_secretario.php');

// --- LÓGICA DO BANCO DE DADOS ---
$turmas = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma")->fetch_all(MYSQLI_ASSOC);
$lista_alunos = [];
$turma_selecionada_nome = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['turma_id'])) {
    $turma_id = $_POST['turma_id'];
    $turma_selecionada_nome = $conexao->query("SELECT nome_turma FROM turmas WHERE id_turma = $turma_id")->fetch_assoc()['nome_turma'];
    
    $sql_alunos = "
        SELECT a.nome_completo AS nome_aluno, u.nome_completo AS nome_responsavel, u.telefone
        FROM alunos a
        LEFT JOIN usuarios u ON a.id_responsavel_principal = u.id_usuario
        WHERE a.id_turma = ?
        ORDER BY a.nome_completo ASC
    ";
    
    $stmt_alunos = $conexao->prepare($sql_alunos);
    $stmt_alunos->bind_param("i", $turma_id);
    $stmt_alunos->execute();
    $lista_alunos = $stmt_alunos->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="container-secretario">
    <h3><i class="fas fa-print"></i> Lista de Alunos para Impressão</h3>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label for="turma_id" class="form-label">Turma</label>
                        <select name="turma_id" id="turma_id" class="form-select" required>
                            <option value="">-- Escolha uma turma --</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?php echo $turma['id_turma']; ?>"><?php echo $turma['nome_turma']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Gerar Lista</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($lista_alunos)): ?>
        <div class="print-area">
            <h3>Lista de Alunos - <?php echo htmlspecialchars($turma_selecionada_nome); ?></h3>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr><th>Nome do Aluno</th><th>Responsável Principal</th><th>Telefone de Contato</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_alunos as $aluno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome_aluno']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_responsavel'] ?? 'Não informado'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['telefone'] ?? 'Não informado'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="window.print()" class="btn btn-success"><i class="fas fa-print"></i> Imprimir</button>
        </div>
    <?php endif; ?>
</div>

<?php require_once('../templates/footer_secretario.php'); ?>