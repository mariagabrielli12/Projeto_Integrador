<?php
$page_title = 'Atestados';
$page_icon = 'fas fa-notes-medical';
require_once 'templates/header_responsavel.php';

// --- BUSCA DE DADOS E LÓGICA (ATUALIZADO) ---

// 1. Busca o ID do aluno associado a este responsável
$id_aluno_associado = null;
$stmt_aluno = $conexao->prepare("SELECT id_aluno FROM alunos_responsaveis WHERE id_responsavel = ? LIMIT 1");
$stmt_aluno->bind_param("i", $id_responsavel_logado);
$stmt_aluno->execute();
$result_aluno = $stmt_aluno->get_result();
if ($result_aluno->num_rows > 0) {
    $id_aluno_associado = $result_aluno->fetch_assoc()['id_aluno'];
}
$stmt_aluno->close();

// 2. Busca o histórico de atestados para este aluno
$atestados = [];
if ($id_aluno_associado) {
    $stmt_hist = $conexao->prepare("SELECT * FROM atestados WHERE id_aluno = ? ORDER BY data_inicio DESC");
    $stmt_hist->bind_param("i", $id_aluno_associado);
    $stmt_hist->execute();
    $result_hist = $stmt_hist->get_result();
    while ($row = $result_hist->fetch_assoc()) {
        $atestados[] = $row;
    }
    $stmt_hist->close();
}
?>

<div class="card">
    <div class="card-header"><i class="fas fa-calendar-check"></i><h3 class="section-title">Gerenciar Atestados</h3></div>
    <div class="card-body">
        <div class="tab-buttons">
            <button class="tab-btn active" onclick="openTab(event, 'novo-atestado')"><i class="fas fa-plus-circle"></i> Enviar Novo Atestado</button>
            <button class="tab-btn" onclick="openTab(event, 'historico')"><i class="fas fa-history"></i> Histórico de Atestados</button>
        </div>

        <div id="novo-atestado" class="tab-content active">
            <form id="form-atestado" method="POST" action="processa_atestado.php" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group"><label>Início do Afastamento*</label><input type="date" name="data_inicio" required></div>
                    <div class="form-group"><label>Fim do Afastamento*</label><input type="date" name="data_fim" required></div>
                </div>
                <div class="form-group"><label>Motivo/Observações</label><textarea name="motivo" rows="3" placeholder="Ex: A criança apresentou febre e dor de garganta."></textarea></div>

                <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar Atestado</button></div>
            </form>
        </div>

        <div id="historico" class="tab-content">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Início Afastamento</th>
                    <th>Fim Afastamento</th>
                    <th>Motivo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($atestados)): ?>
                    <tr><td colspan="4" style="text-align: center;">Nenhum atestado enviado.</td></tr>
                <?php else: ?>
                    <?php foreach ($atestados as $atestado): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($atestado['data_inicio'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($atestado['data_fim'])); ?></td>
                        <td><?php echo htmlspecialchars($atestado['motivo']); ?></td>
                        <td>
                            <a href="detalhe_atestado.php?id=<?php echo $atestado['id_atestado']; ?>" class="btn" style="background-color: #3498db; color: white;">
                                <i class="fas fa-eye"></i> Visualizar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
    </div>
</div>

<script>
    function openTab(evt, tabName) {
        let i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) { tablinks[i].classList.remove("active"); }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.classList.add("active");
    }
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector('.tab-btn.active').click();
        document.getElementById('arquivo_atestado').addEventListener('change', function() {
          
        });
    });
</script>

<?php require_once 'templates/footer_responsavel.php'; ?>