<?php
$page_title = 'Cadastro de Turma';
$page_icon = 'fas fa-layer-group';
require_once '../templates/header_secretario.php';

// Inicializa variáveis
$turma = ['id_turma' => null, 'nome_turma' => '', 'turno' => '', 'id_sala' => '', 'id_professor' => '', 'id_bercarista' => ''];
$is_edit_mode = false;

// Modo Edição
if (isset($_GET['id'])) {
    $is_edit_mode = true;
    $page_title = 'Editar Turma';
    $id_turma = $_GET['id'];
    $stmt = $conexao->prepare("SELECT * FROM turmas WHERE id_turma = ?");
    $stmt->bind_param("i", $id_turma);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) $turma = $result->fetch_assoc();
    $stmt->close();
}

// Processamento do Formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_turma = $_POST['id_turma'] ?: null;
    $nome_turma = $_POST['nome_turma'];
    $turno = $_POST['turno'];
    $id_sala = $_POST['id_sala'];
    $id_professor = $_POST['id_professor'];
    
    // --- AQUI ESTÁ A CORREÇÃO PRINCIPAL ---
    // Se 'id_bercarista' estiver vazio, define como NULL. Caso contrário, converte para inteiro.
    $id_bercarista = !empty($_POST['id_bercarista']) ? (int)$_POST['id_bercarista'] : null;

    if ($id_turma) {
        // UPDATE
        $stmt = $conexao->prepare("UPDATE turmas SET nome_turma = ?, turno = ?, id_sala = ?, id_professor = ?, id_bercarista = ? WHERE id_turma = ?");
        $stmt->bind_param("ssiiii", $nome_turma, $turno, $id_sala, $id_professor, $id_bercarista, $id_turma);
    } else {
        // INSERT
        $stmt = $conexao->prepare("INSERT INTO turmas (nome_turma, turno, id_sala, id_professor, id_bercarista) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $nome_turma, $turno, $id_sala, $id_professor, $id_bercarista);
    }

    if ($stmt->execute()) {
        header("Location: Listagem_Turma.php?sucesso=Turma salva com sucesso!");
    } else {
        header("Location: Cadastro_Turma.php?erro=Erro ao salvar turma: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

// Busca de dados para os dropdowns
$salas = $conexao->query("SELECT id_sala, numero FROM salas WHERE status = 'Disponível' ORDER BY numero");
$professores = $conexao->query("SELECT id_usuario, nome_completo FROM usuarios WHERE id_tipo = 3 ORDER BY nome_completo"); 
$bercaristas = $conexao->query("SELECT id_usuario, nome_completo FROM usuarios WHERE id_tipo = 4 ORDER BY nome_completo"); 
?>

<div class="form-container">
    <form method="POST" action="Cadastro_Turma.php<?php echo $is_edit_mode ? '?id=' . htmlspecialchars($turma['id_turma'] ?? '') : ''; ?>">
        <input type="hidden" name="id_turma" value="<?php echo htmlspecialchars($turma['id_turma'] ?? ''); ?>">
        <h3 class="section-title">Dados da Turma</h3>
        <div class="form-row">
            <div class="form-group"><label>Nome da Turma*</label><input type="text" name="nome_turma" value="<?php echo htmlspecialchars($turma['nome_turma'] ?? ''); ?>" required></div>
            <div class="form-group">
                <label>Turno*</label>
                <select name="turno" required>
                    <option value="Manhã" <?php echo (($turma['turno'] ?? '') == 'Manhã') ? 'selected' : ''; ?>>Manhã</option>
                    <option value="Tarde" <?php echo (($turma['turno'] ?? '') == 'Tarde') ? 'selected' : ''; ?>>Tarde</option>
                    <option value="Integral" <?php echo (($turma['turno'] ?? '') == 'Integral') ? 'selected' : ''; ?>>Integral</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Sala Designada*</label>
                <select name="id_sala" required>
                    <option value="">Selecione a sala</option>
                    <?php if ($salas && $salas->num_rows > 0) { while($sala = $salas->fetch_assoc()): ?>
                        <option value="<?php echo $sala['id_sala']; ?>" <?php echo (($turma['id_sala'] ?? '') == $sala['id_sala']) ? 'selected' : ''; ?>>
                            Sala <?php echo htmlspecialchars($sala['numero']); ?>
                        </option>
                    <?php endwhile; $salas->data_seek(0); } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Professor Responsável*</label>
                <select name="id_professor" required>
                     <option value="">Selecione o professor</option>
                    <?php if ($professores && $professores->num_rows > 0) { while($prof = $professores->fetch_assoc()): ?>
                        <option value="<?php echo $prof['id_usuario']; ?>" <?php echo (($turma['id_professor'] ?? '') == $prof['id_usuario']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($prof['nome_completo']); ?>
                        </option>
                    <?php endwhile; $professores->data_seek(0); } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Berçarista (Opcional)</label>
            <select name="id_bercarista">
                 <option value="">Selecione o berçarista, se aplicável</option>
                <?php if ($bercaristas && $bercaristas->num_rows > 0) { while($berc = $bercaristas->fetch_assoc()): ?>
                    <option value="<?php echo $berc['id_usuario']; ?>" <?php echo (($turma['id_bercarista'] ?? '') == $berc['id_usuario']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($berc['nome_completo']); ?>
                    </option>
                <?php endwhile; $bercaristas->data_seek(0); } ?>
            </select>
        </div>
        <div class="form-actions">
            <a href="Listagem_Turma.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </form>
</div>

<?php require_once '../templates/footer_secretario.php'; ?>