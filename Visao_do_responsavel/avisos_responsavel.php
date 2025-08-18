<?php
$page_title = 'Avisos';
$page_icon = 'fas fa-bell';
require_once '../templates/header_responsavel.php';

// --- LÓGICA DO BANCO DE DADOS ---
// Busca todos os comunicados gerais ou para a turma do aluno
$query = "
    SELECT c.titulo, c.conteudo, c.data_publicacao, u.nome as remetente
    FROM comunicados c
    JOIN usuarios u ON c.remetente_id = u.id
    WHERE 
        c.destinatario_perfil = 'todos' 
        OR c.destinatario_perfil = 'responsaveis'
        -- Adicionar lógica para buscar avisos da turma específica do aluno, se necessário
    ORDER BY c.data_publicacao DESC
";
$resultado = $conexao->query($query);
$avisos = $resultado->fetch_all(MYSQLI_ASSOC);
// --- FIM DA LÓGICA ---
?>

<div class="card">
    <div class="card-header"><i class="fas fa-clipboard-list"></i><h3 class="section-title">Mural de Avisos</h3></div>
    <div class="card-body">
        <?php if (empty($avisos)): ?>
            <p style="text-align: center;">Não há avisos no momento.</p>
        <?php else: ?>
            <?php foreach ($avisos as $aviso): ?>
                <div class="notice-card">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i><?php echo htmlspecialchars($aviso['titulo']); ?></h3>
                    <p class="card-meta">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($aviso['data_publicacao'])); ?></span>
                        <span><i class="fas fa-user"></i> Remetente: <?php echo htmlspecialchars($aviso['remetente']); ?></span>
                    </p>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($aviso['conteudo'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once '../templates/footer_responsavel.php';
?>