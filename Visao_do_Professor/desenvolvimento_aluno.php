<?php
// Garante que o header e a conexão com o banco sejam incluídos
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

// Define as variáveis da página
$page_title = 'Acompanhamento do Desenvolvimento';
$page_icon = 'fas fa-chart-line';
$breadcrumb = 'Portal do Professor > Acompanhamento > Desenvolvimento';

// --- LÓGICA DO BANCO DE DADOS (CORRIGIDA) ---

$id_professor_logado = $_SESSION['id_usuario']; // Usa a variável de sessão correta

// Busca as observações de desenvolvimento mais recentes registradas por este professor
$observacoes = [];
// --- AQUI ESTÁ A CONSULTA CORRIGIDA ---
$sql = "
    SELECT 
        obs.data_observacao, 
        obs.area_desenvolvimento, 
        obs.habilidade_observada, 
        obs.descricao,
        a.nome_completo as nome_aluno -- Corrigido de a.Nome para a.nome_completo
    FROM desenvolvimento_observacoes obs
    JOIN alunos a ON obs.id_aluno = a.id_aluno -- Corrigido para as colunas de ID corretas
    WHERE obs.id_professor = ?                -- Corrigido para id_professor
    ORDER BY obs.data_observacao DESC
    LIMIT 10
";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_professor_logado);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $observacoes = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();
?>

<div class="development-grid">
    <div class="development-card">
      <h3><i class="fas fa-running"></i> Acompanhamento Geral</h3>
      <p>Registe aqui as observações sobre o desenvolvimento motor, cognitivo, social e de linguagem das crianças.</p>
      <a href="registrar_observacao.php" class="btn btn-primary" style="margin-top: 15px;"><i class="fas fa-plus"></i> Registar Nova Observação</a>
    </div>
</div>

<div class="table-container" style="margin-top: 20px;">
    <h4><i class="fas fa-history"></i> Observações Recentes</h4>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Criança</th>
                    <th>Área</th>
                    <th>Habilidade</th>
                    <th>Observação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($observacoes)): ?>
                    <tr><td colspan="6">Nenhuma observação de desenvolvimento registada.</td></tr>
                <?php else: ?>
                    <?php foreach ($observacoes as $obs): ?>
                        <tr>
                            <td><?php echo date("d/m/Y", strtotime($obs['data_observacao'])); ?></td>
                            <td><?php echo htmlspecialchars($obs['nome_aluno']); ?></td>
                            <td><?php echo htmlspecialchars($obs['area_desenvolvimento']); ?></td>
                            <td><?php echo htmlspecialchars($obs['habilidade_observada']); ?></td>
                            <td><?php echo htmlspecialchars($obs['descricao']); ?></td>
                            <td><a href="#" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'templates/footer_professor.php';
?>