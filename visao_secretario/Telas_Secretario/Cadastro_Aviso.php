<?php
$page_title = 'Cadastrar Novo Aviso';
$page_icon = 'fas fa-plus-circle';
require_once '../templates/header_secretario.php';

// Inicializa variáveis
$aviso = ['id_avisos' => null, 'titulo' => '', 'data' => date('Y-m-d'), 'urgencia' => '', 'decricao' => ''];
$is_edit_mode = false;

// Modo Edição
if (isset($_GET['id'])) {
    $is_edit_mode = true;
    $page_title = 'Editar Aviso';
    $id_aviso = $_GET['id'];
    $stmt = $conexao->prepare("SELECT * FROM avisos WHERE id_avisos = ?");
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
    $data = $_POST['data'];
    $categoria = $_POST['categoria']; // No seu DB está como 'urgencia'
    $descricao = $_POST['conteudo']; // No seu DB está como 'decricao'
    $secretario_id = 1; // ID do secretário logado (placeholder)

    if ($id_aviso) {
        $stmt = $conexao->prepare("UPDATE avisos SET titulo = ?, data = ?, urgencia = ?, decricao = ? WHERE id_avisos = ?");
        $stmt->bind_param("ssssi", $titulo, $data, $categoria, $descricao, $id_aviso);
    } else {
        // Precisamos gerar um ID para a tabela 'avisos'
        $result_max_id = $conexao->query("SELECT MAX(id_avisos) as max_id FROM avisos");
        $max_id_row = $result_max_id->fetch_assoc();
        $novo_id = ($max_id_row['max_id'] ?? 0) + 1;

        $stmt = $conexao->prepare("INSERT INTO avisos (id_avisos, titulo, data, urgencia, decricao, secretario_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssi", $novo_id, $titulo, $data, $categoria, $descricao, $secretario_id);
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
            <form id="form-cadastro-aviso" method="POST" action="Cadastro_Aviso.php<?php echo $is_edit_mode ? '?id=' . $aviso['id_avisos'] : ''; ?>">
                <input type="hidden" name="id_aviso" value="<?php echo htmlspecialchars($aviso['id_avisos']); ?>">
                
                <div class="form-group">
                    <label for="aviso-titulo">Título do Aviso*</label>
                    <input type="text" id="aviso-titulo" name="titulo" value="<?php echo htmlspecialchars($aviso['titulo']); ?>" placeholder="Ex: Feriado Nacional, Reunião de Pais" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="aviso-categoria">Categoria*</label>
                        <select id="aviso-categoria" name="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="Administrativo" <?php echo ($aviso['urgencia'] == 'Administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                            <option value="Evento" <?php echo ($aviso['urgencia'] == 'Evento') ? 'selected' : ''; ?>>Evento</option>
                            <option value="Urgente" <?php echo ($aviso['urgencia'] == 'Urgente') ? 'selected' : ''; ?>>Urgente</option>
                            <option value="Pedagógico" <?php echo ($aviso['urgencia'] == 'Pedagógico') ? 'selected' : ''; ?>>Pedagógico</option>
                            <option value="Outros" <?php echo ($aviso['urgencia'] == 'Outros') ? 'selected' : ''; ?>>Outros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="aviso-data">Data do Aviso*</label>
                        <input type="date" id="aviso-data" name="data" value="<?php echo htmlspecialchars($aviso['data']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="aviso-conteudo">Conteúdo do Aviso*</label>
                    <textarea id="aviso-conteudo" name="conteudo" placeholder="Descreva o aviso detalhadamente..." required><?php echo htmlspecialchars($aviso['decricao']); ?></textarea>
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