<?php
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(dirname(__DIR__)));
}
// Inclui a conexão e inicia a sessão ANTES de qualquer HTML
require_once PROJECT_ROOT . '/conexao.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- PROCESSAMENTO DO FORMULÁRIO (SALVAR) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_aluno = $_POST['id_aluno'] ?: null;
    $nome_completo = $_POST['nome_completo'];
    $data_nascimento = $_POST['data_nascimento'];
    $id_turma = $_POST['id_turma'];
    $rg = $_POST['rg']; $cpf = $_POST['cpf']; $cep = $_POST['cep']; $logradouro = $_POST['logradouro']; $numero = $_POST['numero']; $bairro = $_POST['bairro']; $cidade = $_POST['cidade']; $estado = $_POST['estado'];
    $matricula_resp_form = $_POST['matricula_responsavel'];

    // --- LÓGICA PARA ENCONTRAR O ID DO RESPONSÁVEL ---
    $id_responsavel_encontrado = null;
    if (!empty($matricula_resp_form)) {
        $stmt_resp = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE matricula = ? AND id_tipo = 5");
        $stmt_resp->bind_param("s", $matricula_resp_form);
        $stmt_resp->execute();
        $result_resp = $stmt_resp->get_result();
        if ($result_resp->num_rows === 1) {
            $id_responsavel_encontrado = $result_resp->fetch_assoc()['id_usuario'];
        } else {
            $_SESSION['mensagem_erro'] = "Matrícula do responsável não encontrada ou inválida.";
            header("Location: Cadastro_Alunos.php" . ($id_aluno ? '?id=' . $id_aluno : ''));
            exit();
        }
        $stmt_resp->close();
    }
    
    if ($id_aluno) { // UPDATE
        $sql = "UPDATE alunos SET nome_completo=?, data_nascimento=?, rg=?, cpf=?, id_turma=?, cep=?, logradouro=?, numero=?, bairro=?, cidade=?, estado=?, id_responsavel_principal=? WHERE id_aluno = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssissssssii", $nome_completo, $data_nascimento, $rg, $cpf, $id_turma, $cep, $logradouro, $numero, $bairro, $cidade, $estado, $id_responsavel_encontrado, $id_aluno);
    } else { // INSERT
        $sql = "INSERT INTO alunos (nome_completo, data_nascimento, rg, cpf, id_turma, cep, logradouro, numero, bairro, cidade, estado, id_responsavel_principal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssissssssi", $nome_completo, $data_nascimento, $rg, $cpf, $id_turma, $cep, $logradouro, $numero, $bairro, $cidade, $estado, $id_responsavel_encontrado);
    }

    if ($stmt->execute()) {
        $novo_aluno_id = $id_aluno ?: $conexao->insert_id;
        if ($id_responsavel_encontrado) {
            $conexao->query("DELETE FROM alunos_responsaveis WHERE id_aluno = $novo_aluno_id");
            $conexao->query("INSERT INTO alunos_responsaveis (id_aluno, id_responsavel) VALUES ($novo_aluno_id, $id_responsavel_encontrado)");
        }
        $_SESSION['mensagem_sucesso'] = "Aluno salvo com sucesso!";
        header("Location: Listagem_Alunos.php");
        exit();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao salvar aluno: " . $stmt->error;
        header("Location: Cadastro_Alunos.php" . ($id_aluno ? '?id=' . $id_aluno : ''));
        exit();
    }
}

// --- LÓGICA PARA EXIBIÇÃO DA PÁGINA ---
$page_title = 'Cadastro de Aluno';
$page_icon = 'fas fa-user-graduate';
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// Inicializa variáveis para o formulário
$aluno = ['id_aluno' => null, 'nome_completo' => '', 'data_nascimento' => '', 'rg' => '', 'cpf' => '', 'id_turma' => '', 'cep' => '', 'logradouro' => '', 'numero' => '', 'bairro' => '', 'cidade' => '', 'estado' => ''];
$matricula_responsavel = '';
$is_edit_mode = false;

// --- MODO EDIÇÃO ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_edit_mode = true;
    $id_aluno = $_GET['id'];
    $page_title = "Editar Aluno"; 

    $stmt = $conexao->prepare(
        "SELECT a.*, r.matricula as matricula_responsavel 
         FROM alunos a 
         LEFT JOIN usuarios r ON a.id_responsavel_principal = r.id_usuario
         WHERE a.id_aluno = ?"
    );
    $stmt->bind_param("i", $id_aluno);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $aluno_data = $result->fetch_assoc();
        $aluno = $aluno_data;
        $matricula_responsavel = $aluno_data['matricula_responsavel'] ?? '';
    }
    $stmt->close();
}

$turmas_result = $conexao->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma");
?>

<div class="form-container">
    <form method="POST" action="Cadastro_Alunos.php<?php echo $is_edit_mode ? '?id=' . htmlspecialchars($aluno['id_aluno'] ?? '') : ''; ?>">
        <input type="hidden" name="id_aluno" value="<?php echo htmlspecialchars($aluno['id_aluno'] ?? ''); ?>">

        <h3 class="section-title">Dados do Aluno</h3>
        <div class="form-group"><label>Nome Completo*</label><input type="text" name="nome_completo" value="<?php echo htmlspecialchars($aluno['nome_completo'] ?? ''); ?>" required></div>
        <div class="form-row">
            <div class="form-group"><label>RG</label><input type="text" name="rg" value="<?php echo htmlspecialchars($aluno['rg'] ?? ''); ?>"></div>
            <div class="form-group"><label for="cpf">CPF</label><input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($aluno['cpf'] ?? ''); ?>"></div>
        </div>
        <div class="form-group"><label>Data de Nascimento*</label><input type="date" name="data_nascimento" value="<?php echo htmlspecialchars($aluno['data_nascimento'] ?? ''); ?>" required></div>
        
        <h3 class="section-title">Endereço</h3>
        <div class="form-row">
            <div class="form-group"><label for="cep">CEP</label><input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($aluno['cep'] ?? ''); ?>"></div>
            <div class="form-group"><label>Logradouro</label><input type="text" name="logradouro" value="<?php echo htmlspecialchars($aluno['logradouro'] ?? ''); ?>"></div>
            <div class="form-group"><label>Número</label><input type="text" name="numero" value="<?php echo htmlspecialchars($aluno['numero'] ?? ''); ?>"></div>
            <div class="form-group"><label>Bairro</label><input type="text" name="bairro" value="<?php echo htmlspecialchars($aluno['bairro'] ?? ''); ?>"></div>
            <div class="form-group"><label>Cidade</label><input type="text" name="cidade" value="<?php echo htmlspecialchars($aluno['cidade'] ?? ''); ?>"></div>
            <div class="form-group"><label>Estado</label><input type="text" name="estado" value="<?php echo htmlspecialchars($aluno['estado'] ?? ''); ?>"></div>
        </div>

        <h3 class="section-title">Dados da Matrícula e Responsável</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="id_turma">Turma*</label>
                <select id="id_turma" name="id_turma" required>
                    <option value="">Selecione a turma</option>
                    <?php if ($turmas_result->num_rows > 0) {
                        while($turma = $turmas_result->fetch_assoc()): ?>
                            <option value="<?php echo $turma['id_turma']; ?>" <?php echo (($aluno['id_turma'] ?? '') == $turma['id_turma']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($turma['nome_turma']); ?>
                            </option>
                        <?php endwhile; } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="matricula_responsavel">Matrícula do Responsável Principal</label>
                <input type="text" id="matricula_responsavel" name="matricula_responsavel" value="<?php echo htmlspecialchars($matricula_responsavel); ?>" placeholder="Insira a matrícula para vincular">
            </div>
        </div>
        
        <div class="form-actions">
            <a href="Listagem_Alunos.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </form>
</div>

<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        IMask(document.getElementById('cpf'), { mask: '000.000.000-00' });
        IMask(document.getElementById('cep'), { mask: '00000-000' });
    });
</script>

<?php
require_once PROJECT_ROOT . '/visao_secretario/templates/footer_secretario.php';
?>