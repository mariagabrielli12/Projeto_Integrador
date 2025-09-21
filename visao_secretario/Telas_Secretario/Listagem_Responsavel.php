<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Listagem de Responsáveis';
$page_icon = 'fas fa-user-tie';
require_once PROJECT_ROOT . '/visao_secretario/templates/header_secretario.php';

// --- LÓGICA DE FILTRO ---
$filtro_busca = isset($_GET['busca']) ? $conexao->real_escape_string($_GET['busca']) : '';

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    // ... (código de exclusão permanece o mesmo)
    $id_para_deletar = $_GET['delete_id'];
    $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_para_deletar);
    if ($stmt->execute()) {
        header("Location: Listagem_Responsavel.php?sucesso=Responsável excluído com sucesso!");
    } else {
        header("Location: Listagem_Responsavel.php?erro=Erro ao excluir responsável.");
    }
    $stmt->close();
    exit();
}

// --- LÓGICA DE CONSULTA COM FILTRO ---
$sql = "SELECT id_usuario, nome_completo, cpf, email, telefone FROM usuarios WHERE id_tipo = 5";
if (!empty($filtro_busca)) {
    $sql .= " AND (nome_completo LIKE '%$filtro_busca%' OR cpf LIKE '%$filtro_busca%')";
}
$sql .= " ORDER BY nome_completo ASC";
$resultado = $conexao->query($sql);

?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Filtro de Busca</h3></div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="busca">Buscar por Nome ou CPF</label>
                    <input type="text" id="busca" name="busca" class="form-control" value="<?php echo htmlspecialchars($filtro_busca); ?>" placeholder="Digite para buscar...">
                </div>
                 <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-container" style="margin-top: 20px;">
    <div class="table-settings">
        <a href="Cadastro_Responsavel.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Cadastrar Novo Responsável</a>
    </div>

     <?php if(isset($_GET['sucesso'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['erro'])): ?>
        <div class="alert error"><?php echo htmlspecialchars($_GET['erro']); ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($responsavel = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($responsavel['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($responsavel['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($responsavel['email']); ?></td>
                        <td><?php echo htmlspecialchars($responsavel['telefone'] ?? 'N/D'); ?></td>
                        <td class="action-buttons">
                            <a href="Cadastro_Responsavel.php?id=<?php echo $responsavel['id_usuario']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="Listagem_Responsavel.php?delete_id=<?php echo $responsavel['id_usuario']; ?>" class="btn-icon" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum responsável encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../templates/footer_secretario.php';
 ?>