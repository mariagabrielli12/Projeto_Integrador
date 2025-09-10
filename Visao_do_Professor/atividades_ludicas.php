<?php
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH . '/Visao_do_Professor/templates/header_professor.php');

$page_title = 'Atividades Lúdicas';
$page_icon = 'fas fa-puzzle-piece';
$breadcrumb = 'Portal do Professor > Planeamento > Atividades Lúdicas';

// Busca as atividades lúdicas já cadastradas
$atividades_ludicas = [];
$sql = "
    SELECT al.*, u.nome_completo as nome_professor
    FROM atividades_ludicas al
    LEFT JOIN usuarios u ON al.id_professor_criador = u.id_usuario
    ORDER BY al.titulo ASC
";
$result = $conexao->query($sql);
if ($result) {
    $atividades_ludicas = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="card">
    <div class="tab-buttons">
        <button class="tab-btn active" onclick="openTab(event, 'listar-atividades')">
            <i class="fas fa-list"></i> Listar Atividades
        </button>
        <button class="tab-btn" onclick="openTab(event, 'cadastrar-atividade')">
            <i class="fas fa-plus-circle"></i> Cadastrar Nova
        </button>
    </div>

    <div id="listar-atividades" class="tab-content active">
        <div class="card-header"><h3 class="section-title">Biblioteca de Atividades Lúdicas</h3></div>
        <div class="card-body">
            <div id="activities-list" class="activities-container">
                <?php if (empty($atividades_ludicas)): ?>
                    <p>Nenhuma atividade lúdica cadastrada.</p>
                <?php else: ?>
                    <?php foreach ($atividades_ludicas as $activity): ?>
                        <div class="activity-card">
                            <div class="activity-header"><h3><?php echo htmlspecialchars($activity['titulo']); ?></h3></div>
                            <div class="activity-body">
                                <div class="activity-info"><i class="fas fa-tags"></i><span>Categoria: <?php echo htmlspecialchars($activity['categoria'] ?? 'N/D'); ?></span></div>
                                <div class="activity-info"><i class="fas fa-clock"></i><span>Duração: <?php echo htmlspecialchars($activity['duracao_sugerida'] ?? 'N/D'); ?></span></div>
                                <div class="activity-info"><i class="fas fa-bullseye"></i><span>Objetivo: <?php echo htmlspecialchars($activity['objetivos'] ?? 'N/D'); ?></span></div>
                                <div class="activity-info"><i class="fas fa-tools"></i><span>Materiais: <?php echo htmlspecialchars($activity['materiais'] ?? 'N/D'); ?></span></div>
                                <div class="activity-info"><i class="fas fa-user"></i><span>Criado por: <?php echo htmlspecialchars($activity['nome_professor'] ?? 'Sistema'); ?></span></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="cadastrar-atividade" class="tab-content">
        <div class="card-header"><h3 class="section-title">Cadastrar Nova Atividade Lúdica</h3></div>
        <div class="card-body">
            <form id="form-ludica" method="POST" action="processa_atividade_ludica.php">
                <div class="form-group"><label>Título da Atividade*</label><input type="text" name="titulo" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Categoria</label><input type="text" name="categoria" placeholder="Ex: Motora, Cognitiva"></div>
                    <div class="form-group"><label>Duração Sugerida</label><input type="text" name="duracao" placeholder="Ex: 30 minutos"></div>
                </div>
                <div class="form-group"><label>Objetivos</label><textarea name="objetivos" rows="3"></textarea></div>
                <div class="form-group"><label>Materiais Necessários</label><textarea name="materiais" rows="3"></textarea></div>
                <div class="form-actions">
                    <button class="btn btn-secondary" type="reset"><i class="fas fa-times"></i> Limpar</button>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Cadastrar Atividade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extra_js = '<script>
    function openTab(evt, tabName) {
      let i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tab-content");
      for (i = 0; i < tabcontent.length; i++) { tabcontent[i].classList.remove("active"); }
      tablinks = document.getElementsByClassName("tab-btn");
      for (i = 0; i < tablinks.length; i++) { tablinks[i].classList.remove("active"); }
      document.getElementById(tabName).classList.add("active");
      evt.currentTarget.classList.add("active");
    }
     document.addEventListener("DOMContentLoaded", function() {
        openTab({currentTarget: document.querySelector(".tab-btn.active")}, "listar-atividades");
    });
</script>';
require_once 'templates/footer_professor.php';
?>