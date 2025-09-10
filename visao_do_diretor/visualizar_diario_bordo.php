<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Visualização do Diário de Bordo';
$page_icon = 'fas fa-book-open';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE CONSULTA ---
$sql = "
    SELECT 
        db.data_registro,
        db.titulo,
        db.observacoes,
        t.nome_turma,
        u.nome_completo as nome_professor
    FROM diario_bordo db
    JOIN turmas t ON db.id_turma = t.id_turma
    JOIN usuarios u ON db.id_professor = u.id_usuario
    ORDER BY db.data_registro DESC
";
$resultado = $conexao->query($sql);
?>

<div class="table-container">
    <div class="table-settings">
        <h3 class="section-title" style="margin: 0;">Todos os Registos do Diário de Bordo</h3>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Turma</th>
                <th>Professor</th>
                <th>Título do Registo</th>
                <th>Observações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($registo = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($registo['data_registro'])); ?></td>
                        <td><?php echo htmlspecialchars($registo['nome_turma']); ?></td>
                        <td><?php echo htmlspecialchars($registo['nome_professor']); ?></td>
                        <td><?php echo htmlspecialchars($registo['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($registo['observacoes']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum registo no diário de bordo encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once PROJECT_ROOT . '/visao_do_diretor/templates/footer_diretor.php';
?>