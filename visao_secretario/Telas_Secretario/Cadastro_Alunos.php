<?php
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(dirname(__DIR__)));
}
$page_title = 'Cadastro de Aluno';
$page_icon = 'fas fa-user-graduate';
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// Inicializa o array de aluno
$aluno = ['id_aluno' => null, 'nome_completo' => '', 'data_nascimento' => '', 'rg' => '', 'cpf' => '', 'id_turma' => '', 'cep' => '', 'logradouro' => '', 'numero' => '', 'complemento' => '', 'bairro' => '', 'cidade' => '', 'estado' => '', 'contato_responsavel' => '', 'email_responsavel' => ''];
$is_edit_mode = false;

// --- MODO EDIÇÃO ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_edit_mode = true;
    $id_aluno = $_GET['id'];
    $page_title = "Editar Aluno"; 

    $stmt = $conexao->prepare("SELECT * FROM alunos WHERE id_aluno = ?");
    $stmt->bind_param("i", $id_aluno);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $aluno = $result->fetch_assoc();
    }
    $stmt->close();
}

// --- PROCESSAMENTO DO FORMULÁRIO (SALVAR) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta todos os dados do formulário
    $id_aluno = $_POST['id_aluno'] ?: null;
    $nome_completo = $_POST['nome_completo'];
    $data_nascimento = $_POST['data_nascimento'];
    $rg = $_POST['rg'];
    $cpf = $_POST['cpf'];
    $id_turma = $_POST['id_turma'];
    $cep = $_POST['cep'];
    $logradouro = $_POST['logradouro'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $contato_responsavel = $_POST['contato_responsavel'];
    $email_responsavel = $_POST['email_responsavel'];

    if ($id_aluno) {
        // LÓGICA DE UPDATE
        $sql = "UPDATE alunos SET nome_completo=?, data_nascimento=?, rg=?, cpf=?, id_turma=?, cep=?, logradouro=?, numero=?, bairro=?, cidade=?, estado=?, contato_responsavel=?, email_responsavel=? WHERE id_aluno = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssissssssssi", $nome_completo, $data_nascimento, $rg, $cpf, $id_turma, $cep, $logradouro, $numero, $bairro, $cidade, $estado, $contato_responsavel, $email_responsavel, $id_aluno);
    } else {
        // LÓGICA DE INSERT
        $sql = "INSERT INTO alunos (nome_completo, data_nascimento, rg, cpf, id_turma, cep, logradouro, numero, bairro, cidade, estado, contato_responsavel, email_responsavel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssissssssss", $nome_completo, $data_nascimento, $rg, $cpf, $id_turma, $cep, $logradouro, $numero, $bairro, $cidade, $estado, $contato_responsavel, $email_responsavel);
    }

    if ($stmt->execute()) {
        header("Location: Listagem_Alunos.php?sucesso=Aluno salvo com sucesso!");
    } else {
        header("Location: Cadastro_Alunos.php?erro=Erro ao salvar aluno: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

// Busca as turmas para o dropdown
$turmas_result = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma");
?>

<div class="form-container">
    <form method="POST" action="Cadastro_Alunos.php<?php echo $is_edit_mode ? '?id=' . htmlspecialchars($aluno['id_aluno']) : ''; ?>">
        <input type="hidden" name="id_aluno" value="<?php echo htmlspecialchars($aluno['id_aluno']); ?>">

        <h3 class="section-title">Dados do Aluno</h3>
        <div class="form-group"><label for="nome_completo">Nome Completo*</label><input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($aluno['nome_completo']); ?>" required></div>
        <div class="form-row">
            <div class="form-group"><label for="rg">RG</label><input type="text" id="rg" name="rg" value="<?php echo htmlspecialchars($aluno['rg']); ?>"></div>
            <div class="form-group"><label for="cpf">CPF</label><input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($aluno['cpf']); ?>"></div>
        </div>
        <div class="form-group"><label for="data_nascimento">Data de Nascimento*</label><input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($aluno['data_nascimento']); ?>" required></div>
        
        <h3 class="section-title">Endereço</h3>
        <div class="form-row">
            <div class="form-group"><label for="cep">CEP</label><input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($aluno['cep']); ?>"></div>
            <div class="form-group"><label for="logradouro">Logradouro</label><input type="text" id="logradouro" name="logradouro" value="<?php echo htmlspecialchars($aluno['logradouro']); ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label for="numero">Número</label><input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($aluno['numero']); ?>"></div>
            <div class="form-group"><label for="bairro">Bairro</label><input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($aluno['bairro']); ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label for="cidade">Cidade</label><input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($aluno['cidade']); ?>"></div>
            <div class="form-group"><label for="estado">Estado</label><input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($aluno['estado']); ?>"></div>
        </div>

        <h3 class="section-title">Dados da Matrícula</h3>
        <div class="form-group">
            <label for="id_turma">Turma*</label>
            <select id="id_turma" name="id_turma" required>
                <option value="">Selecione a turma</option>
                <?php if ($turmas_result->num_rows > 0) {
                    while($turma = $turmas_result->fetch_assoc()): ?>
                        <option value="<?php echo $turma['id_turma']; ?>" <?php echo ($aluno['id_turma'] == $turma['id_turma']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($turma['nome_turma']); ?>
                        </option>
                    <?php endwhile; } ?>
            </select>
        </div>
        <div class="form-row">
             <div class="form-group"><label for="contato_responsavel">Telefone (Responsável)*</label><input type="tel" id="contato_responsavel" name="contato_responsavel" value="<?php echo htmlspecialchars($aluno['contato_responsavel']); ?>" required></div>
            <div class="form-group"><label for="email_responsavel">E-mail (Responsável)*</label><input type="email" id="email_responsavel" name="email_responsavel" value="<?php echo htmlspecialchars($aluno['email_responsavel']); ?>" required></div>
        </div>
        
        <div class="form-actions">
            <a href="Listagem_Alunos.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </form>
</div>

<?php
require_once PROJECT_ROOT . '/visao_secretario/templates/footer_secretario.php';
?>