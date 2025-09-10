<?php
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

$page_title = 'Plano de Atividades';
$page_icon = 'fas fa-calendar-day';
$breadcrumb = 'Portal do Professor > Planeamento > Plano de Atividades';

// --- LÓGICA DO BANCO DE DADOS ATUALIZADA ---

// 1. Busca as turmas associadas a este professor
$turmas = [];
$stmt_turmas = $conexao->prepare("SELECT id_turma, nome_turma FROM turmas WHERE id_professor = ?");
$stmt_turmas->bind_param("i", $id_professor_logado);
$stmt_turmas->execute();
$result_turmas = $stmt_turmas->get_result();
if($result_turmas) {
    $turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);
}
$stmt_turmas->close();

// 2. Busca as atividades já cadastradas por este professor
$atividades_planeadas = [];
$sql_atividades = "
    SELECT at.titulo, at.data_atividade, at.descricao, t.nome_turma
    FROM atividades at
    JOIN turmas t ON at.id_turma = t.id_turma
    WHERE at.id_professor = ?
    ORDER BY at.data_atividade DESC
";
$stmt_atividades = $conexao->prepare($sql_atividades);
$stmt_atividades->bind_param("i", $id_professor_logado);
$stmt_atividades->execute();
$result_atividades = $stmt_atividades->get_result();
if($result_atividades){
    $atividades_planeadas = $result_atividades->fetch_all(MYSQLI_ASSOC);
}
$stmt_atividades->close();
?>

<div class="card">
    <div class="tab-buttons">
        <button class="tab-btn active" onclick="openTab(event, 'cadastrar-atividade')">
            <i class="fas fa-plus-circle"></i> Cadastrar Atividade
        </button>
        <button class="tab-btn" onclick="openTab(event, 'visualizar-plano')">
            <i class="fas fa-calendar-alt"></i> Visualizar Plano
        </button>
    </div>

    <div id="cadastrar-atividade" class="tab-content active">
        <div class="card-header"><h3 class="section-title">Cadastrar Nova Atividade no Plano</h3></div>
        <div class="card-body">
            <form id="form-atividade" method="POST" action="processa_plano_atividades.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="activity-title">Título da Atividade*</label>
                        <input type="text" id="activity-title" name="titulo" placeholder="Ex: Roda de Leitura" required>
                    </div>
                    <div class="form-group">
                        <label for="activity-class">Turma*</label>
                        <select id="activity-class" name="turma_id" required>
                            <option value="">Selecione a Turma</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?php echo $turma['id_turma']; ?>"><?php echo htmlspecialchars($turma['nome_turma']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="activity-date">Data e Hora*</label>
                    <input type="datetime-local" name="data_atividade" id="activity-date" required>
                </div>
                <div class="form-group">
                    <label for="activity-description">Descrição/Objetivos*</label>
                    <textarea id="activity-description" name="descricao" placeholder="Descreva os objetivos e como a atividade será realizada" rows="4" required></textarea>
                </div>
                <div class="form-actions">
                    <button class="btn btn-secondary" type="reset"><i class="fas fa-times"></i> Limpar</button>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Salvar Atividade</button>
                </div>
            </form>
        </div>
    </div>

    <div id="visualizar-plano" class="tab-content">
        <div class="card-header"><h3 class="section-title">Atividades Planeadas</h3></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Data</th><th>Turma</th><th>Atividade</th><th>Descrição</th></tr></thead>
                    <tbody>
                        <?php if (empty($atividades_planeadas)): ?>
                            <tr><td colspan="4">Nenhuma atividade planeada encontrada.</td></tr>
                        <?php else: ?>
                            <?php foreach ($atividades_planeadas as $atividade): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($atividade['data_atividade'])); ?></td>
                                <td><?php echo htmlspecialchars($atividade['nome_turma']); ?></td>
                                <td><?php echo htmlspecialchars($atividade['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($atividade['descricao']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Adiciona o JavaScript necessário para as abas funcionarem
$extra_js = '<script>
    function openTab(evt, tabName) {
      let i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tab-content");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
      }
      tablinks = document.getElementsByClassName("tab-btn");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
      }
      document.getElementById(tabName).classList.add("active");
      evt.currentTarget.classList.add("active");
    }
    document.addEventListener("DOMContentLoaded", function() {
        openTab({currentTarget: document.querySelector(".tab-btn.active")}, "cadastrar-atividade");
    });
</script>';
require_once 'templates/footer_professor.php';
?>