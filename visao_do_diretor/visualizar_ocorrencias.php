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
        o.data_ocorrencia, 
        o.tipo, 
        o.descricao, 
        o.status,
        a.nome_completo as nome_aluno,
        u.nome_completo as nome_registrou
    FROM ocorrencias o
    JOIN alunos a ON o.id_aluno = a.id_aluno
    LEFT JOIN usuarios u ON o.id_registrado_por = u.id_usuario
    ORDER BY o.data_ocorrencia DESC
";
$resultado = $conexao->query($sql);
?>

<div class="table-container">
    <div class="table-settings">
        <h3 class="section-title" style="margin: 0;">Todas as Ocorrências Registadas</h3>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Aluno</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Registado Por</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($ocorrencia = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($ocorrencia['data_ocorrencia'])); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['nome_aluno']); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['tipo']); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['nome_registrou'] ?? 'Sistema'); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Nenhuma ocorrência encontrada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// CORREÇÃO: Usa o caminho correto para o footer
require_once VIEW_ROOT . '/templates/footer.php';
?>