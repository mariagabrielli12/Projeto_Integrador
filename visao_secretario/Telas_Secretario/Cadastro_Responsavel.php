<?php
// Define a constante que aponta para a pasta raiz do projeto
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(dirname(__DIR__)));
}
// Inclui a conexão e inicia a sessão ANTES de qualquer coisa
require_once PROJECT_ROOT . '/conexao.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- PROCESSAMENTO DO FORMULÁRIO (SALVAR) ---
// Este bloco agora está no topo para evitar erros de "headers already sent"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conexao->begin_transaction();
    try {
        $id_responsavel = $_POST['id_usuario'] ?: null;
        $nome = $_POST['nome_completo'];
        $cpf = $_POST['cpf'];
        $rg = $_POST['rg'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $matricula = $_POST['matricula'];
        $senha = $_POST['senha']; 
        $tipo_id = 5; // ID para Responsável

        if ($id_responsavel) { // UPDATE
            if (!empty($senha)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt_user = $conexao->prepare("UPDATE usuarios SET nome_completo=?, cpf=?, rg=?, email=?, telefone=?, matricula=?, senha_hash=? WHERE id_usuario=?");
                $stmt_user->bind_param("sssssssi", $nome, $cpf, $rg, $email, $telefone, $matricula, $senha_hash, $id_responsavel);
            } else {
                $stmt_user = $conexao->prepare("UPDATE usuarios SET nome_completo=?, cpf=?, rg=?, email=?, telefone=?, matricula=? WHERE id_usuario=?");
                $stmt_user->bind_param("ssssssi", $nome, $cpf, $rg, $email, $telefone, $matricula, $id_responsavel);
            }
        } else { // INSERT
            if (empty($senha)) {
                throw new Exception("A senha é obrigatória para novos responsáveis.");
            }
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt_user = $conexao->prepare("INSERT INTO usuarios (nome_completo, cpf, rg, email, telefone, matricula, senha_hash, id_tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_user->bind_param("sssssssi", $nome, $cpf, $rg, $email, $telefone, $matricula, $senha_hash, $tipo_id);
        }
        
        // Executa e verifica se houve erro
        if (!$stmt_user->execute()) {
            throw new Exception("Erro ao salvar dados do usuário: " . $stmt_user->error);
        }
        
        $id_responsavel_final = $id_responsavel ?: $conexao->insert_id;
        $stmt_user->close();

        // Dados do Endereço
        $id_endereco = $_POST['id_endereco'] ?: null;
        $cep = $_POST['cep']; $logradouro = $_POST['logradouro']; $numero = $_POST['numero']; $complemento = $_POST['complemento']; $bairro = $_POST['bairro']; $cidade = $_POST['cidade']; $estado = $_POST['estado'];

        if ($id_endereco) {
            $stmt_addr = $conexao->prepare("UPDATE enderecos SET cep=?, logradouro=?, numero=?, complemento=?, bairro=?, cidade=?, estado=? WHERE id_endereco=?");
            $stmt_addr->bind_param("sssssssi", $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $id_endereco);
        } else {
            // Verifica se existe um endereço para este usuário antes de inserir
            $check_addr = $conexao->query("SELECT id_endereco FROM enderecos WHERE id_usuario = $id_responsavel_final");
            if($check_addr->num_rows == 0){
                $stmt_addr = $conexao->prepare("INSERT INTO enderecos (id_usuario, cep, logradouro, numero, complemento, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_addr->bind_param("isssssss", $id_responsavel_final, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $estado);
            }
        }
        
        if (isset($stmt_addr)) {
            if (!$stmt_addr->execute()) {
                throw new Exception("Erro ao salvar dados de endereço: " . $stmt_addr->error);
            }
            $stmt_addr->close();
        }
        
        $conexao->commit();
        $_SESSION['mensagem_sucesso'] = "Responsável salvo com sucesso!";
        header("Location: Listagem_Responsavel.php");
        exit();
    } catch (Exception $e) {
        $conexao->rollback();
        // Adiciona a mensagem de erro específica à sessão para ser exibida
        $_SESSION['mensagem_erro'] = "Erro ao salvar: " . $e->getMessage();
        header("Location: Cadastro_Responsavel.php" . ($id_responsavel ? '?id=' . $id_responsavel : ''));
        exit();
    }
}

// --- LÓGICA PARA EXIBIÇÃO DA PÁGINA ---
$page_title = 'Cadastro de Responsável';
$page_icon = 'fas fa-user-tie';
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// Inicializa arrays
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
?>

<div class="form-container">
    <form id="form-responsavel" method="POST" action="Cadastro_Responsavel.php<?php echo $is_edit_mode ? '?id=' . htmlspecialchars($responsavel['id_usuario'] ?? '') : ''; ?>">
        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($responsavel['id_usuario'] ?? ''); ?>">
        <input type="hidden" name="id_endereco" value="<?php echo htmlspecialchars($endereco['id_endereco'] ?? ''); ?>">

        <h3 class="section-title">Dados do Responsável</h3>
        <div class="form-row">
            <div class="form-group"><label>Nome Completo*</label><input type="text" name="nome_completo" value="<?php echo htmlspecialchars($responsavel['nome_completo'] ?? ''); ?>" required></div>
            <div class="form-group"><label for="cpf">CPF*</label><input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($responsavel['cpf'] ?? ''); ?>" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>RG</label><input type="text" name="rg" value="<?php echo htmlspecialchars($responsavel['rg'] ?? ''); ?>"></div>
            <div class="form-group"><label for="telefone">Telefone*</label><input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($responsavel['telefone'] ?? ''); ?>" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>E-mail*</label><input type="email" name="email" value="<?php echo htmlspecialchars($responsavel['email'] ?? ''); ?>" required></div>
        </div>

        <h3 class="section-title">Endereço</h3>
        <div class="form-row">
            <div class="form-group"><label for="cep">CEP</label><input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($endereco['cep'] ?? ''); ?>"></div>
            <div class="form-group"><label>Logradouro</label><input type="text" name="logradouro" value="<?php echo htmlspecialchars($endereco['logradouro'] ?? ''); ?>"></div>
            <div class="form-group"><label>Número</label><input type="text" name="numero" value="<?php echo htmlspecialchars($endereco['numero'] ?? ''); ?>"></div>
            <div class="form-group"><label>Bairro</label><input type="text" name="bairro" value="<?php echo htmlspecialchars($endereco['bairro'] ?? ''); ?>"></div>
            <div class="form-group"><label>Cidade</label><input type="text" name="cidade" value="<?php echo htmlspecialchars($endereco['cidade'] ?? ''); ?>"></div>
            <div class="form-group"><label>Estado</label><input type="text" name="estado" value="<?php echo htmlspecialchars($endereco['estado'] ?? ''); ?>"></div>
        </div>

        <h3 class="section-title">Credenciais de Acesso</h3>
        <div class="form-row">
            <div class="form-group"><label>Matrícula (ou Usuário)*</label><input type="text" name="matricula" value="<?php echo htmlspecialchars($responsavel['matricula'] ?? ''); ?>" required></div>
            <div class="form-group">
                <label for="senha">Senha*</label>
                <input type="password" id="senha" name="senha" placeholder="<?php echo $is_edit_mode ? 'Deixe em branco para não alterar' : 'Senha de acesso'; ?>" <?php echo !$is_edit_mode ? 'required' : ''; ?>>
            </div>
        </div>

        <div class="form-actions">
            <a href="Listagem_Responsavel.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Cadastro</button>
        </div>
    </form>
</div>

<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        IMask(document.getElementById('cpf'), { mask: '000.000.000-00' });
        IMask(document.getElementById('cep'), { mask: '00000-000' });
        IMask(document.getElementById('telefone'), {
            mask: [ { mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' } ]
        });
    });
</script>

<?php require_once '../templates/footer_secretario.php';
 ?>