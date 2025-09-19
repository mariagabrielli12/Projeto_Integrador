<?php
// Garante que o header e a conexão com o banco sejam incluídos
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

// Define as variáveis da página
$page_title = 'Novo Comunicado';
$page_icon = 'fas fa-bullhorn';
$breadcrumb = 'Portal do Professor > Comunicados > Novo Comunicado';

// --- LÓGICA DO BANCO DE DADOS ---
// Pega o ID e nome do professor da sessão (usando os nomes de coluna do novo schema)
$id_professor_logado = $_SESSION['id_usuario'] ?? 1; // Ajustado para id_usuario
$nome_professor_logado = $_SESSION['nome_completo'] ?? 'Professor(a)'; // Ajustado para nome_completo

// Busca as turmas do professor para popular a lista de destinatários (usando id_professor)
$turmas = [];
$stmt_turmas = $conexao->prepare("SELECT id_turma, nome_turma FROM turmas WHERE id_professor = ?");
$stmt_turmas->bind_param("i", $id_professor_logado);
$stmt_turmas->execute();
$result_turmas = $stmt_turmas->get_result();
if($result_turmas) {
    $turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);
}
$stmt_turmas->close();
// --- FIM DA LÓGICA ---
?>

<div class="card">
  <div class="card-header"><h3>Detalhes do Comunicado</h3></div>
  <div class="card-body">
    <form id="novo-comunicado-form" method="POST" action="processa_comunicado.php">
      <div class="form-row">
        <div class="form-group">
          <label for="data-envio">Data de Envio</label>
          <input type="text" id="data-envio" value="<?php echo date('d/m/Y H:i'); ?>" readonly>
        </div>
        <div class="form-group">
          <label for="remetente">Remetente</label>
          <input type="text" id="remetente" value="<?php echo htmlspecialchars($nome_professor_logado); ?>" readonly>
        </div>
      </div>
      <div class="form-group">
        <label for="destinatario">Destinatário*</label>
        <select id="destinatario" name="destinatario_turma_id" required>
          <option value="">Selecione o destinatário</option>
          <option value="secretaria">Secretaria / Coordenação</option>
          <?php foreach($turmas as $turma): ?>
            <option value="<?php echo $turma['id_turma']; ?>">Pais/Responsáveis - <?php echo htmlspecialchars($turma['nome_turma']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="assunto">Assunto do Comunicado*</label>
        <input type="text" id="assunto" name="assunto" placeholder="Ex: Reunião de pais, evento escolar..." required>
      </div>
      <div class="form-group">
        <label for="mensagem">Mensagem*</label>
        <textarea id="mensagem" name="mensagem" rows="8" placeholder="Escreva o conteúdo do comunicado aqui..." required></textarea>
      </div>
      <div class="form-actions">
        <a href="comunicados_professor.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar Comunicado</button>
      </div>
    </form>
  </div>
</div>

<?php
require_once 'templates/footer_professor.php';
?>