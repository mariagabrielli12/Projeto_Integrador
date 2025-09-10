<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Editar Utilizador';
$page_icon = 'fas fa-user-edit';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// Valida o ID do utilizador vindo da URL
$id_usuario_para_editar = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_usuario_para_editar === 0) {
    $_SESSION['mensagem_erro'] = "Utilizador inválido!";
    header("Location: index.php"); // Redireciona se não houver ID
    exit;
}

// Busca os dados atuais do utilizador no banco
$stmt = $conexao->prepare("SELECT nome_completo, cpf, email, telefone, matricula, id_tipo, ativo FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario_para_editar);
$stmt->execute();
$utilizador = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$utilizador) {
    $_SESSION['mensagem_erro'] = "Utilizador não encontrado!";
    header("Location: index.php");
    exit;
}
?>

<div class="form-container">
    <form method="POST" action="processa_edicao_usuario.php">
        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario_para_editar; ?>">
        <h3 class="section-title">A Editar: <?php echo htmlspecialchars($utilizador['nome_completo']); ?></h3>

        <div class="form-row">
            <div class="form-group"><label>Nome Completo*</label><input type="text" name="nome_completo" value="<?php echo htmlspecialchars($utilizador['nome_completo']); ?>" required></div>
            <div class="form-group"><label>CPF*</label><input type="text" name="cpf" value="<?php echo htmlspecialchars($utilizador['cpf']); ?>" required></div>
        </div>
        
        <div class="form-row">
            <div class="form-group"><label>Email*</label><input type="email" name="email" value="<?php echo htmlspecialchars($utilizador['email']); ?>" required></div>
            <div class="form-group"><label>Telefone</label><input type="tel" name="telefone" value="<?php echo htmlspecialchars($utilizador['telefone']); ?>"></div>
        </div>

        <div class="form-row">
            <div class="form-group"><label>Matrícula (Utilizador)*</label><input type="text" name="matricula" value="<?php echo htmlspecialchars($utilizador['matricula']); ?>" required></div>
            <div class="form-group">
                <label for="ativo">Status*</label>
                <select id="ativo" name="ativo" required>
                    <option value="1" <?php echo $utilizador['ativo'] ? 'selected' : ''; ?>>Ativo</option>
                    <option value="0" <?php echo !$utilizador['ativo'] ? 'selected' : ''; ?>>Inativo</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Alterar Senha (deixe em branco para não mudar)</label>
            <input type="password" name="senha" placeholder="Nova senha">
        </div>
        
        <div class="form-actions">
            <a href="javascript:history.back()" class="btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
        </div>
    </form>
</div>

<?php
require_once PROJECT_ROOT . '/visao_do_diretor/templates/footer_diretor.php';
?>