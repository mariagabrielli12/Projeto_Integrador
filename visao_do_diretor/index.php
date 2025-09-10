<?php
// CORREÇÃO 1: Definimos a raiz da visão do diretor
define('VIEW_ROOT', __DIR__); 
// CORREÇÃO 2: A raiz do projeto está um nível acima
define('PROJECT_ROOT', dirname(VIEW_ROOT)); 

$page_title = 'Dashboard do Diretor';
$page_icon = 'fas fa-tachometer-alt';

// CORREÇÃO 3: Usamos o caminho correto para o header
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DO DASHBOARD ---
$total_alunos = $conexao->query("SELECT COUNT(*) as total FROM alunos")->fetch_assoc()['total'];
$total_funcionarios = $conexao->query("SELECT COUNT(*) as total FROM usuarios WHERE id_tipo IN (2, 3, 4)")->fetch_assoc()['total'];
$total_turmas = $conexao->query("SELECT COUNT(*) as total FROM turmas")->fetch_assoc()['total'];
$ocorrencias_recentes = $conexao->query(
    "SELECT o.descricao, o.data_ocorrencia, a.nome_completo as nome_aluno
     FROM ocorrencias o
     JOIN alunos a ON o.id_aluno = a.id_aluno
     ORDER BY o.data_ocorrencia DESC LIMIT 3"
);
?>

<div class="summary-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
    <div class="summary-card" style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
        <h3><?php echo $total_alunos; ?></h3>
        <p>Alunos Matriculados</p>
    </div>
    <div class="summary-card" style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
        <h3><?php echo $total_funcionarios; ?></h3>
        <p>Funcionários Ativos</p>
    </div>
    <div class="summary-card" style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
        <h3><?php echo $total_turmas; ?></h3>
        <p>Turmas Formadas</p>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="section-title">Ocorrências Recentes</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Data</th><th>Aluno</th><th>Descrição</th></tr></thead>
                <tbody>
                    <?php if ($ocorrencias_recentes && $ocorrencias_recentes->num_rows > 0): ?>
                        <?php while($oc = $ocorrencias_recentes->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($oc['data_ocorrencia'])); ?></td>
                                <td><?php echo htmlspecialchars($oc['nome_aluno']); ?></td>
                                <td><?php echo htmlspecialchars($oc['descricao']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Nenhuma ocorrência recente.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-actions" style="margin-top: 15px;">
            <a href="visualizar_ocorrencias.php" class="btn btn-primary">Ver Todas as Ocorrências</a>
        </div>
    </div>
</div>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>