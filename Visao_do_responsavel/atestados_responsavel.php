<?php
$page_title = 'Atestados';
$page_icon = 'fas fa-notes-medical';
require_once '../templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS ---
// Busca o histórico de atestados do aluno associado ao responsável logado
$stmt = $conexao->prepare("SELECT * FROM atestados WHERE aluno_id = ? AND responsavel_id = ? ORDER BY data_envio DESC");
$stmt->bind_param("ii", $aluno_id_associado, $responsavel_logado_id);
$stmt->execute();
$atestados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
// --- FIM DA LÓGICA ---
?>

<div class="card">
    <div class="card-header"><i class="fas fa-calendar-check"></i><h3 class="section-title">Gerenciar Atestados</h3></div>
    <div class="card-body">
        <div class="tab-buttons">
            <button class="tab-btn active" onclick="openTab(event, 'novo-atestado')"><i class="fas fa-plus-circle"></i> Enviar Novo Atestado</button>
            <button class="tab-btn" onclick="openTab(event, 'historico')"><i class="fas fa-history"></i> Histórico de Atestados</button>
        </div>

        <div id="novo-atestado" class="tab-content active">
            <form id="form-atestado" action="processa_atestado.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="inicio-afastamento">Início do Afastamento*</label>
                        <input type="date" id="inicio-afastamento" name="data_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fim-afastamento">Fim do Afastamento*</label>
                        <input type="date" id="fim-afastamento" name="data_fim" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="observacoes">Motivo/Observações (Opcional)</label>
                    <textarea id="observacoes" name="motivo" rows="3" placeholder="Ex: A criança apresentou febre e dor de garganta."></textarea>
                </div>

                <div class="file-upload-container">
                    <label for="arquivo-atestado" class="file-upload">
                        <input type="file" id="arquivo-atestado" name="arquivo_atestado" accept=".pdf,.jpg,.jpeg,.png" required>
                        <span class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Clique para enviar o atestado</span>
                            <p>Formatos: PDF, JPG, PNG (máx. 5MB)</p>
                        </span>
                    </label>
                    <div id="file-info" class="file-info"><i class="fas fa-file-alt"></i> Nenhum arquivo selecionado</div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar Atestado</button>
                </div>
            </form>
        </div>

        <div id="historico" class="tab-content">
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Início Afastamento</th><th>Fim Afastamento</th><th>Status</th><th>Enviado em</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php if (empty($atestados)): ?>
                            <tr><td colspan="5" style="text-align: center;">Nenhum atestado enviado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($atestados as $atestado): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($atestado['data_inicio_afastamento'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($atestado['data_fim_afastamento'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($atestado['status']); ?>">
                                        <?php echo htmlspecialchars($atestado['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($atestado['data_envio'])); ?></td>
                                <td>
                                    <a href="detalhe_atestado.php?id=<?php echo $atestado['id']; ?>" class="btn btn-view"><i class="fas fa-eye"></i> Detalhes</a>
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

<?php
// Adiciona o script de abas e de feedback de upload ao final da página
$extra_js = '<script>
    function openTab(evt, tabName) {
        var i, tabContent, tabButtons;
        tabContent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabContent.length; i++) { tabContent[i].style.display = "none"; }
        tabButtons = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tabButtons.length; i++) { tabButtons[i].classList.remove("active"); }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.classList.add("active");
    }
    document.getElementById("arquivo-atestado").addEventListener("change", function(e) {
        const fileInfo = document.getElementById("file-info");
        if (this.files.length > 0) {
            fileInfo.innerHTML = `<i class="fas fa-file-check"></i> ${this.files[0].name}`;
        } else {
            fileInfo.innerHTML = \'<i class="fas fa-file-alt"></i> Nenhum arquivo selecionado\';
        }
    });
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".tab-btn.active").click();
    });
</script>';
require_once '../templates/footer_responsavel.php';
?>