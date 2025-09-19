<?php
$page_title = 'Cadastrar Novo Aviso';
$page_icon = 'fas fa-plus-circle';
require_once '../templates/header_secretario.php';

// Inicializa variáveis
$aviso = ['id_aviso' => null, 'titulo' => '', 'data_aviso' => date('Y-m-d'), 'categoria' => '', 'descricao' => ''];
$is_edit_mode = false;

// Modo Edição
if (isset($_GET['id'])) {
    $is_edit_mode = true;
    $page_title = 'Editar Aviso';
    $id_aviso = $_GET['id'];
    $stmt = $conexao->prepare("SELECT * FROM avisos WHERE id_aviso = ?");
    $stmt->bind_param("i", $id_aviso);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) $aviso = $result->fetch_assoc();
    $stmt->close();
}

// Processamento do Formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aviso = $_POST['id_aviso'] ?: null;
    $titulo = $_POST['titulo'];
    $data = $_POST['data_aviso'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];
    $secretario_id = $_SESSION['id_usuario']; // Pega o ID do secretário logado

    if ($id_aviso) {
        $stmt = $conexao->prepare("UPDATE avisos SET titulo = ?, data_aviso = ?, categoria = ?, descricao = ? WHERE id_aviso = ?");
        $stmt->bind_param("ssssi", $titulo, $data, $categoria, $descricao, $id_aviso);
    } else {
        $stmt = $conexao->prepare("INSERT INTO avisos (titulo, data_aviso, categoria, descricao, id_secretario) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $titulo, $data, $categoria, $descricao, $secretario_id);
    }

    if ($stmt->execute()) {
        header("Location: avisos_secretario.php?sucesso=Aviso salvo com sucesso!");
    } else {
        header("Location: Cadastro_Aviso.php?erro=Erro ao salvar o aviso.");
    }
    $stmt->close();
    exit();
}
?>

<div class="form-container">
    <div class="card">
        <div class="card-header"><h3 class="section-title">Informações do Aviso</h3></div>
        <div class="card-body">
            <form id="form-cadastro-aviso" method="POST" action="Cadastro_Aviso.php<?php echo $is_edit_mode ? '?id=' . htmlspecialchars($aviso['id_aviso'] ?? '') : ''; ?>">
                <input type="hidden" name="id_aviso" value="<?php echo htmlspecialchars($aviso['id_aviso'] ?? ''); ?>">
                
                <div class="form-group">
                    <label for="aviso-titulo">Título do Aviso*</label>
                    <input type="text" id="aviso-titulo" name="titulo" value="<?php echo htmlspecialchars($aviso['titulo'] ?? ''); ?>" placeholder="Ex: Feriado Nacional, Reunião de Pais" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="aviso-categoria">Categoria*</label>
                        <select id="aviso-categoria" name="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="Administrativo" <?php echo (($aviso['categoria'] ?? '') == 'Administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                            <option value="Evento" <?php echo (($aviso['categoria'] ?? '') == 'Evento') ? 'selected' : ''; ?>>Evento</option>
                            <option value="Urgente" <?php echo (($aviso['categoria'] ?? '') == 'Urgente') ? 'selected' : ''; ?>>Urgente</option>
                            <option value="Pedagógico" <?php echo (($aviso['categoria'] ?? '') == 'Pedagógico') ? 'selected' : ''; ?>>Pedagógico</option>
                            <option value="Outros" <?php echo (($aviso['categoria'] ?? '') == 'Outros') ? 'selected' : ''; ?>>Outros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="aviso-data">Data do Aviso*</label>
                        <input type="date" id="aviso-data" name="data_aviso" value="<?php echo htmlspecialchars($aviso['data_aviso'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="aviso-conteudo">Conteúdo do Aviso*</label>
                    <textarea id="aviso-conteudo" name="descricao" placeholder="Descreva o aviso detalhadamente..." required><?php echo htmlspecialchars($aviso['descricao'] ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="avisos_secretario.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Aviso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../templates/footer_secretario.php'; ?>