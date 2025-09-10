<?php

$page_title = 'Cadastro de Sala';
$page_icon = 'fas fa-door-open';
require_once 'templates/header_secretario.php';

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
require_once PROJECT_ROOT . '/templates/footer.php';
?>