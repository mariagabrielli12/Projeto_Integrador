<?php
$page_title = 'Cadastro de Responsável';
$page_icon = 'fas fa-user-tie';
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// Inicializa arrays com valores padrão
$responsavel = ['id_usuario' => null, 'nome_completo' => '', 'cpf' => '', 'rg' => '', 'data_nascimento' => '', 'email' => '', 'telefone' => '', 'matricula' => ''];
$endereco = ['id_endereco' => null, 'logradouro' => '', 'cep' => '', 'numero' => '', 'complemento' => '', 'bairro' => '', 'cidade' => '', 'estado' => ''];
$is_edit_mode = false;

// --- MODO EDIÇÃO ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_edit_mode = true;
    $id_responsavel = $_GET['id'];
    $page_title = 'Editar Responsável';

    $stmt = $conexao->prepare("SELECT u.*, e.* FROM usuarios u LEFT JOIN enderecos e ON u.id_usuario = e.id_usuario WHERE u.id_usuario = ? AND u.id_tipo = 5");
    $stmt->bind_param("i", $id_responsavel);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $responsavel)) $responsavel[$key] = $value;
            if (array_key_exists($key, $endereco)) $endereco[$key] = $value;
        }
    }
    $stmt->close();
}

// --- PROCESSAMENTO DO FORMULÁRIO (SALVAR) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conexao->begin_transaction();
    try {
        // Dados do Responsável (tabela: usuarios)
        $id_responsavel = $_POST['id_usuario'] ?: null;
        $nome = $_POST['nome_completo'];
        $cpf = $_POST['cpf'];
        $rg = $_POST['rg'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $matricula = $_POST['matricula'];
        $tipo_id = 5; // ID para 'Responsavel'

        if ($id_responsavel) {
            $stmt_user = $conexao->prepare("UPDATE usuarios SET nome_completo=?, cpf=?, rg=?, email=?, telefone=?, matricula=? WHERE id_usuario=?");
            $stmt_user->bind_param("ssssssi", $nome, $cpf, $rg, $email, $telefone, $matricula, $id_responsavel);
        } else {
            // Senha padrão para novos responsáveis (deve ser trocada no primeiro login)
            $senha_padrao = password_hash('creche123', PASSWORD_DEFAULT);
            $stmt_user = $conexao->prepare("INSERT INTO usuarios (nome_completo, cpf, rg, email, telefone, matricula, senha_hash, id_tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_user->bind_param("sssssssi", $nome, $cpf, $rg, $email, $telefone, $matricula, $senha_padrao, $tipo_id);
        }
        $stmt_user->execute();
        
        // Pega o ID do responsável (seja novo ou existente)
        $id_responsavel_final = $id_responsavel ?: $conexao->insert_id;
        $stmt_user->close();

        // Dados do Endereço (tabela: enderecos)
        $id_endereco = $_POST['id_endereco'] ?: null;
        $cep = $_POST['cep'];
        $logradouro = $_POST['logradouro'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];

        if ($id_endereco) {
            $stmt_addr = $conexao->prepare("UPDATE enderecos SET cep=?, logradouro=?, numero=?, complemento=?, bairro=?, cidade=?, estado=? WHERE id_endereco=?");
            $stmt_addr->bind_param("sssssssi", $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $id_endereco);
        } else {
            $stmt_addr = $conexao->prepare("INSERT INTO enderecos (id_usuario, cep, logradouro, numero, complemento, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_addr->bind_param("isssssss", $id_responsavel_final, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $estado);
        }
        $stmt_addr->execute();
        $stmt_addr->close();
        
        $conexao->commit();
        header("Location: Listagem_Responsavel.php?sucesso=Responsável salvo com sucesso!");
        exit();
    } catch (Exception $e) {
        $conexao->rollback();
        header("Location: Cadastro_Responsavel.php?erro=Erro ao salvar: " . $e->getMessage());
        exit();
    }
}
?>

<div class="form-container">
    <form id="form-responsavel" method="POST" action="Cadastro_Responsavel.php<?php echo $is_edit_mode ? '?id=' . htmlspecialchars($responsavel['id_usuario']) : ''; ?>">
        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($responsavel['id_usuario']); ?>">
        <input type="hidden" name="id_endereco" value="<?php echo htmlspecialchars($endereco['id_endereco']); ?>">

        <h3 class="section-title">Dados do Responsável</h3>
        <div class="form-row">
            <div class="form-group"><label>Nome Completo*</label><input type="text" name="nome_completo" value="<?php echo htmlspecialchars($responsavel['nome_completo']); ?>" required></div>
            <div class="form-group"><label>RG</label><input type="text" name="rg" value="<?php echo htmlspecialchars($responsavel['rg']); ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>CPF*</label><input type="text" name="cpf" value="<?php echo htmlspecialchars($responsavel['cpf']); ?>" required></div>
            <div class="form-group"><label>Telefone*</label><input type="tel" name="telefone" value="<?php echo htmlspecialchars($responsavel['telefone']); ?>" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>E-mail*</label><input type="email" name="email" value="<?php echo htmlspecialchars($responsavel['email']); ?>" required></div>
            <div class="form-group"><label>Matrícula (ou Usuário)*</label><input type="text" name="matricula" value="<?php echo htmlspecialchars($responsavel['matricula']); ?>" required></div>
        </div>

        <h3 class="section-title">Endereço</h3>
        <div class="form-row">
            <div class="form-group"><label>CEP</label><input type="text" name="cep" value="<?php echo htmlspecialchars($endereco['cep']); ?>"></div>
            <div class="form-group"><label>Logradouro</label><input type="text" name="logradouro" value="<?php echo htmlspecialchars($endereco['logradouro']); ?>"></div>
            <div class="form-group"><label>Número</label><input type="text" name="numero" value="<?php echo htmlspecialchars($endereco['numero']); ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Complemento</label><input type="text" name="complemento" value="<?php echo htmlspecialchars($endereco['complemento']); ?>"></div>
            <div class="form-group"><label>Bairro</label><input type="text" name="bairro" value="<?php echo htmlspecialchars($endereco['bairro']); ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Cidade</label><input type="text" name="cidade" value="<?php echo htmlspecialchars($endereco['cidade']); ?>"></div>
            <div class="form-group"><label>Estado</label><input type="text" name="estado" value="<?php echo htmlspecialchars($endereco['estado']); ?>"></div>
        </div>

        <div class="form-actions">
            <a href="Listagem_Responsavel.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Cadastro</button>
        </div>
    </form>
</div>

<?php require_once PROJECT_ROOT . '/visao_secretario/templates/footer_secretario.php'; ?>