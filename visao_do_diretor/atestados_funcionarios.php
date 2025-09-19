<?php
define('VIEW_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(VIEW_ROOT));

$page_title = 'Atestados de Funcionários';
$page_icon = 'fas fa-file-medical';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE PROCESSAMENTO (CRIAR/EDITAR) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_atestado = $_POST['id_atestado'] ?: null;
    $id_usuario = $_POST['id_usuario'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $motivo = $_POST['motivo'];

    if ($id_atestado) {
        $stmt = $conexao->prepare("UPDATE atestados_funcionarios SET id_usuario = ?, data_inicio = ?, data_fim = ?, motivo = ? WHERE id_atestado = ?");
        $stmt->bind_param("isssi", $id_usuario, $data_inicio, $data_fim, $motivo, $id_atestado);
    } else {
        $stmt = $conexao->prepare("INSERT INTO atestados_funcionarios (id_usuario, data_inicio, data_fim, motivo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_usuario, $data_inicio, $data_fim, $motivo);
    }

    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = "Atestado salvo com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao salvar atestado.";
    }
    $stmt->close();
    header("Location: atestados_funcionarios.php");
    exit();
}

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['excluir_id'])) {
    $id_atestado = (int)$_GET['excluir_id'];
    $stmt = $conexao->prepare("DELETE FROM atestados_funcionarios WHERE id_atestado = ?");
    $stmt->bind_param("i", $id_atestado);
    $stmt->execute();
    $_SESSION['mensagem_sucesso'] = "Atestado excluído com sucesso!";
    header("Location: atestados_funcionarios.php");
    exit();
}

// --- BUSCA DE DADOS PARA FORMULÁRIO E LISTA ---
$atestado_para_editar = null;
if (isset($_GET['editar_id'])) {
    $stmt = $conexao->prepare("SELECT * FROM atestados_funcionarios WHERE id_atestado = ?");
    $stmt->bind_param("i", (int)$_GET['editar_id']);
    $stmt->execute();
    $atestado_para_editar = $stmt->get_result()->fetch_assoc();
}

// Busca funcionários e agrupa por tipo para o JavaScript
$funcionarios_query = $conexao->query("SELECT id_usuario, nome_completo, id_tipo FROM usuarios WHERE id_tipo IN (2,3,4) ORDER BY nome_completo");
$funcionarios_por_tipo = [];
while ($func = $funcionarios_query->fetch_assoc()) {
    $funcionarios_por_tipo[$func['id_tipo']][] = $func;
}

// Consulta principal para a lista de histórico (sem filtros)
$atestados_lista = $conexao->query("
    SELECT af.*, u.nome_completo 
    FROM atestados_funcionarios af 
    JOIN usuarios u ON af.id_usuario = u.id_usuario 
    ORDER BY af.data_inicio DESC
")->fetch_all(MYSQLI_ASSOC);

?>

<div class="form-container">
    <div class="card">
        <div class="card-header"><h3 class="section-title"><?php echo $atestado_para_editar ? 'Editar Atestado' : 'Registrar Novo Atestado'; ?></h3></div>
        <div class="card-body">
            <form method="POST" action="atestados_funcionarios.php">
                <input type="hidden" name="id_atestado" value="<?php echo $atestado_para_editar['id_atestado'] ?? ''; ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label>Função*</label>
                        <select id="form_funcao_id" onchange="atualizarFuncionarios('form_funcao_id', 'form_funcionario_id');" required>
                            <option value="">Selecione a função</option>
                            <option value="2">Secretário</option>
                            <option value="3">Professor</option>
                            <option value="4">Berçarista</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Funcionário*</label>
                        <select name="id_usuario" id="form_funcionario_id" required disabled>
                            <option value="">Selecione a função primeiro</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Início do Afastamento*</label><input type="date" name="data_inicio" value="<?php echo $atestado_para_editar['data_inicio'] ?? date('Y-m-d'); ?>" required></div>
                    <div class="form-group"><label>Fim do Afastamento*</label><input type="date" name="data_fim" value="<?php echo $atestado_para_editar['data_fim'] ?? date('Y-m-d'); ?>" required></div>
                </div>
                <div class="form-group"><label>Motivo</label><textarea name="motivo" rows="3"><?php echo htmlspecialchars($atestado_para_editar['motivo'] ?? ''); ?></textarea></div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                    <?php if ($atestado_para_editar): ?>
                        <a href="atestados_funcionarios.php" class="btn btn-secondary">Cancelar Edição</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="table-container" style="margin-top: 20px;">
    <div class="table-settings"><h3 class="section-title">Histórico de Atestados</h3></div>
    <table class="table">
        <thead><tr><th>Funcionário</th><th>Início</th><th>Fim</th><th>Motivo</th><th>Ações</th></tr></thead>
        <tbody>
            <?php if (empty($atestados_lista)): ?>
                <tr><td colspan="5">Nenhum atestado registrado.</td></tr>
            <?php else: ?>
                <?php foreach($atestados_lista as $atestado): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($atestado['nome_completo']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($atestado['data_inicio'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($atestado['data_fim'])); ?></td>
                        <td><?php echo htmlspecialchars($atestado['motivo']); ?></td>
                        <td class="action-buttons">
                            <a href="atestados_funcionarios.php?editar_id=<?php echo $atestado['id_atestado']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="atestados_funcionarios.php?excluir_id=<?php echo $atestado['id_atestado']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    const funcionariosPorTipo = <?php echo json_encode($funcionarios_por_tipo); ?>;
    const atestadoAtual = <?php echo json_encode($atestado_para_editar); ?>;

    function atualizarFuncionarios(selectFuncaoId, selectFuncionarioId) {
        const funcaoSelect = document.getElementById(selectFuncaoId);
        const funcionarioSelect = document.getElementById(selectFuncionarioId);
        const tipoId = funcaoSelect.value;

        funcionarioSelect.innerHTML = '<option value="">Selecione um funcionário</option>';
        funcionarioSelect.disabled = true;

        if (tipoId && tipoId !== "" && funcionariosPorTipo[tipoId]) {
            funcionariosPorTipo[tipoId].forEach(func => {
                const option = document.createElement('option');
                option.value = func.id_usuario;
                option.textContent = func.nome_completo;
                funcionarioSelect.appendChild(option);
            });
            funcionarioSelect.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Se estiver no modo de edição, pré-seleciona os campos
        if (atestadoAtual) {
            const idUsuario = atestadoAtual.id_usuario;
            // Encontra a função do usuário
            for (const tipo in funcionariosPorTipo) {
                if (funcionariosPorTipo[tipo].some(func => func.id_usuario == idUsuario)) {
                    document.getElementById('form_funcao_id').value = tipo;
                    atualizarFuncionarios('form_funcao_id', 'form_funcionario_id');
                    document.getElementById('form_funcionario_id').value = idUsuario;
                    break;
                }
            }
        }
    });
</script>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>