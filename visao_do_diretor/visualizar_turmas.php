<?php
// CORREÇÃO 1: Definimos a raiz da visão do diretor
define('VIEW_ROOT', __DIR__); 
// CORREÇÃO 2: A raiz do projeto está um nível acima
define('PROJECT_ROOT', dirname(VIEW_ROOT)); 

$page_title = 'Visualização de turmas';
$page_icon = 'fas fa-tachometer-alt';

// CORREÇÃO 3: Usamos o caminho correto para o header
require_once VIEW_ROOT . '/templates/header_diretor.php';

// --- LÓGICA DE CONSULTA ---
$sql = "
    SELECT 
        t.id_turma, 
        t.nome_turma, 
        t.turno,
        s.numero as numero_sala,
        p.nome_completo as nome_professor,
        b.nome_completo as nome_bercarista,
        (SELECT COUNT(*) FROM alunos WHERE id_turma = t.id_turma) as num_alunos
    FROM turmas t
    LEFT JOIN salas s ON t.id_sala = s.id_sala
    LEFT JOIN usuarios p ON t.id_professor = p.id_usuario
    LEFT JOIN usuarios b ON t.id_bercarista = b.id_usuario
    ORDER BY t.nome_turma ASC
";
$resultado = $conexao->query($sql);
?>

<div class="table-container">
    <div class="table-settings">
        <h3 class="section-title" style="margin: 0;">Todas as Turmas Registadas</h3>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nome da Turma</th>
                <th>Turno</th>
                <th>Nº de Alunos</th>
                <th>Professor</th>
                <th>Berçarista</th>
                <th>Sala</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($turma = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($turma['nome_turma']); ?></td>
                        <td><?php echo htmlspecialchars($turma['turno']); ?></td>
                        <td><?php echo $turma['num_alunos']; ?></td>
                        <td><?php echo htmlspecialchars($turma['nome_professor'] ?? 'N/D'); ?></td>
                        <td><?php echo htmlspecialchars($turma['nome_bercarista'] ?? 'N/A'); ?></td>
                        <td>Sala <?php echo htmlspecialchars($turma['numero_sala'] ?? 'N/D'); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Nenhuma turma encontrada no sistema.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>