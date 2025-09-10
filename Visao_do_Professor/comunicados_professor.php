<?php
// Garante que o header e a conexão com o banco sejam incluídos
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

// Define as variáveis da página
$page_title = 'Comunicados';
$page_icon = 'fas fa-comment-dots';
$breadcrumb = 'Portal do Professor > Comunicados';

// --- LÓGICA DO BANCO DE DADOS ---
// Pega o ID do professor da sessão
$id_professor_logado = $_SESSION['id_usuario'] ?? 1; // Ajustado para id_usuario

// Busca os comunicados enviados por este professor
$comunicados = [];
$sql = "
    SELECT 
        c.data_envio, 
        c.assunto, 
        COALESCE(t.nome_turma, 'Secretaria/Coordenação') as destinatario
    FROM comunicados c
    LEFT JOIN turmas t ON c.destinatario_turma_id = t.id_turma
    WHERE c.remetente_id = ?
    ORDER BY c.data_envio DESC
";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_professor_logado);
$stmt->execute();
$result = $stmt->get_result();
if($result) {
    $comunicados = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();
// --- FIM DA LÓGICA ---
?>

<div class="card">
  <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h3>Meus Comunicados Enviados</h3>
    <a href="criar_comunicado.php" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Comunicado</a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Data de Envio</th>
              <th>Assunto</th>
              <th>Destinatário</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($comunicados)): ?>
                <tr><td colspan="4">Nenhum comunicado enviado por você.</td></tr>
            <?php else: ?>
                <?php foreach ($comunicados as $comunicado): ?>
                <tr>
                  <td><?php echo date('d/m/Y H:i', strtotime($comunicado['data_envio'])); ?></td>
                  <td><?php echo htmlspecialchars($comunicado['assunto']); ?></td>
                  <td><?php echo htmlspecialchars($comunicado['destinatario']); ?></td>
                  <td>
                    <button class="btn-icon" title="Visualizar"><i class="fas fa-eye"></i></button>
                  </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
    </div>
  </div>
</div>

<?php
require_once 'templates/footer_professor.php';
?>