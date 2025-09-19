<?php
define('VIEW_ROOT', __DIR__); 
define('PROJECT_ROOT', dirname(VIEW_ROOT)); 

$page_title = 'Gestão de Funcionários';
$page_icon = 'fas fa-users-cog';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE FILTRO ---
$filtro_tipo = isset($_GET['tipo']) ? (int)$_GET['tipo'] : 0;
$filtro_nome = isset($_GET['nome']) ? $conexao->real_escape_string($_GET['nome']) : '';

// --- LÓGICA DE CONSULTA COM FILTRO ---
$sql = "SELECT u.id_usuario, u.nome_completo, u.cpf, u.email, u.ativo, tu.nome_tipo 
        FROM usuarios u
        JOIN tipos_usuario tu ON u.id_tipo = tu.id_tipo
        WHERE u.id_tipo IN (2, 3, 4)"; // Apenas funcionários

if ($filtro_tipo > 0) {
    $sql .= " AND u.id_tipo = $filtro_tipo";
}
if (!empty($filtro_nome)) {
    $sql .= " AND (u.nome_completo LIKE '%$filtro_nome%' OR u.cpf LIKE '%$filtro_nome%')";
}
$sql .= " ORDER BY u.nome_completo ASC";
$resultado = $conexao->query($sql);
?>

<div class="card">
    <div class="card-header"><h3 class="section-title">Filtros de Busca</h3></div>
    <div class="card-body">
        <form method="GET" action="listagem_funcionarios.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="tipo">Filtrar por Função</label>
                    <select id="tipo" name="tipo" onchange="this.form.submit()">
                        <option value="0">Todos os Funcionários</option>
                        <option value="2" <?php if($filtro_tipo == 2) echo 'selected'; ?>>Secretários</option>
                        <option value="3" <?php if($filtro_tipo == 3) echo 'selected'; ?>>Professores</option>
                        <option value="4" <?php if($filtro_tipo == 4) echo 'selected'; ?>>Berçaristas</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nome">Buscar por Nome ou CPF</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($filtro_nome); ?>" placeholder="Digite o nome ou CPF...">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                <a href="listagem_funcionarios.php" class="btn btn-secondary">Limpar Filtros</a>
            </div>
        </form>
    </div>
</div>

<div class="table-container">
    <div class="table-settings">
        <a href="cadastro_usuario.php" class="btn-cadastrar"><i class="fas fa-plus"></i> Cadastrar Novo Funcionário</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Função</th>
                <th>CPF</th>
                <th>Email</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($funcionario = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($funcionario['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($funcionario['nome_tipo']); ?></td>
                        <td><?php echo htmlspecialchars($funcionario['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($funcionario['email']); ?></td>
                        <td><span class="status-badge <?php echo $funcionario['ativo'] ? 'active' : 'inactive'; ?>"><?php echo $funcionario['ativo'] ? 'Ativo' : 'Inativo'; ?></span></td>
                        <td class="action-buttons">
                            <a href="editar_usuario.php?id=<?php echo $funcionario['id_usuario']; ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Nenhum funcionário encontrado para os filtros selecionados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>