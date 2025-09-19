<?php
define('VIEW_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(VIEW_ROOT));

$page_title = 'Gestão de Avisos';
$page_icon = 'fas fa-bell';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE PROCESSAMENTO DO FORMULÁRIO (CRIAR/EDITAR) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aviso = $_POST['id_aviso'] ?: null;
    $titulo = $_POST['titulo'];
    $data_aviso = $_POST['data_aviso'];
    $categoria = $_POST['categoria'];
    $destinatario = $_POST['destinatario']; // Novo campo
    $descricao = $_POST['descricao'];
    $id_criador = $_SESSION['id_usuario'];

    if ($id_aviso) {
        $stmt = $conexao->prepare("UPDATE avisos SET titulo = ?, data_aviso = ?, categoria = ?, destinatario = ?, descricao = ? WHERE id_aviso = ?");
        $stmt->bind_param("sssssi", $titulo, $data_aviso, $categoria, $destinatario, $descricao, $id_aviso);
    } else {
        $stmt = $conexao->prepare("INSERT INTO avisos (titulo, data_aviso, categoria, destinatario, descricao, id_secretario) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $titulo, $data_aviso, $categoria, $destinatario, $descricao, $id_criador);
    }

    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = "Aviso salvo com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao salvar o aviso.";
    }
    $stmt->close();
    header("Location: avisos_diretor.php");
    exit();
}

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['excluir_id'])) {
    $id_aviso = (int)$_GET['excluir_id'];
    $stmt = $conexao->prepare("DELETE FROM avisos WHERE id_aviso = ?");
    $stmt->bind_param("i", $id_aviso);
    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = "Aviso excluído com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao excluir o aviso.";
    }
    $stmt->close();
    header("Location: avisos_diretor.php");
    exit();
}

// --- LÓGICA PARA BUSCAR AVISOS (PARA LISTAGEM E EDIÇÃO) ---
$aviso_para_editar = null;
if (isset($_GET['editar_id'])) {
    $id_aviso_edicao = (int)$_GET['editar_id'];
    $stmt = $conexao->prepare("SELECT * FROM avisos WHERE id_aviso = ?");
    $stmt->bind_param("i", $id_aviso_edicao);
    $stmt->execute();
    $aviso_para_editar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$todos_avisos = $conexao->query("
    SELECT a.*, u.nome_completo as criador
    FROM avisos a
    LEFT JOIN usuarios u ON a.id_secretario = u.id_usuario
    ORDER BY a.data_aviso DESC
")->fetch_all(MYSQLI_ASSOC);

?>

<div class="form-container">
    <div class="card">
        <div class="card-header">
            <h3 class="section-title"><?php echo $aviso_para_editar ? 'Editar Aviso' : 'Criar Novo Aviso'; ?></h3>
        </div>
        <div class="card-body">
            <form method="POST" action="avisos_diretor.php">
                <input type="hidden" name="id_aviso" value="<?php echo $aviso_para_editar['id_aviso'] ?? ''; ?>">
                <div class="form-group">
                    <label>Título*</label>
                    <input type="text" name="titulo" value="<?php echo htmlspecialchars($aviso_para_editar['titulo'] ?? ''); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Categoria*</label>
                        <select name="categoria" required>
                            <option value="Geral" <?php echo (($aviso_para_editar['categoria'] ?? '') == 'Geral') ? 'selected' : ''; ?>>Geral</option>
                            <option value="Evento" <?php echo (($aviso_para_editar['categoria'] ?? '') == 'Evento') ? 'selected' : ''; ?>>Evento</option>
                            <option value="Urgente" <?php echo (($aviso_para_editar['categoria'] ?? '') == 'Urgente') ? 'selected' : ''; ?>>Urgente</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Destinatário*</label>
                        <select name="destinatario" required>
                            <option value="GERAL" <?php echo (($aviso_para_editar['destinatario'] ?? '') == 'GERAL') ? 'selected' : ''; ?>>Geral (Pais e Funcionários)</option>
                            <option value="FUNCIONARIOS" <?php echo (($aviso_para_editar['destinatario'] ?? '') == 'FUNCIONARIOS') ? 'selected' : ''; ?>>Apenas Funcionários</option>
                            <option value="SECRETARIA" <?php echo (($aviso_para_editar['destinatario'] ?? '') == 'SECRETARIA') ? 'selected' : ''; ?>>Apenas Secretaria</option>
                        </select>
                    </div>
                </div>
                 <div class="form-group">
                    <label>Data do Aviso*</label>
                    <input type="date" name="data_aviso" value="<?php echo $aviso_para_editar['data_aviso'] ?? date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Descrição*</label>
                    <textarea name="descricao" rows="4" required><?php echo htmlspecialchars($aviso_para_editar['descricao'] ?? ''); ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Aviso</button>
                    <?php if ($aviso_para_editar): ?>
                        <a href="avisos_diretor.php" class="btn btn-secondary">Cancelar Edição</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="table-container" style="margin-top: 20px;">
    <div class="table-settings">
        <h3 class="section-title">Histórico de Avisos</h3>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Título</th>
                <th>Destinatário</th>
                <th>Criado por</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($todos_avisos)): ?>
                <tr><td colspan="5">Nenhum aviso cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach($todos_avisos as $aviso): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($aviso['data_aviso'])); ?></td>
                        <td><?php echo htmlspecialchars($aviso['titulo']); ?></td>
                        <td><span class="status-badge active"><?php echo htmlspecialchars($aviso['destinatario']); ?></span></td>
                        <td><?php echo htmlspecialchars($aviso['criador'] ?? 'Sistema'); ?></td>
                        <td class="action-buttons">
                            <a href="avisos_diretor.php?editar_id=<?php echo $aviso['id_aviso']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="avisos_diretor.php?excluir_id=<?php echo $aviso['id_aviso']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este aviso?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>