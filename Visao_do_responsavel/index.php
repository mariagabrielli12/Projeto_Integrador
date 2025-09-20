<?php
$page_title = 'Página Inicial';
$page_icon = 'fas fa-home';
// O header agora cuida da sessão, conexão e da lógica de múltiplos filhos,
// disponibilizando a variável $id_aluno_logado.
require_once 'templates/header_responsavel.php';

// --- BUSCA DE DADOS DINÂMICOS PARA O DASHBOARD ---

// 1. Busca as informações do aluno atualmente selecionado
$aluno_info = [];
if ($id_aluno_logado > 0) {
    $stmt_aluno = $conexao->prepare("SELECT nome_completo FROM alunos WHERE id_aluno = ?");
    $stmt_aluno->bind_param("i", $id_aluno_logado);
    $stmt_aluno->execute();
    $result_aluno = $stmt_aluno->get_result();
    if ($result_aluno->num_rows > 0) {
        $aluno_info = $result_aluno->fetch_assoc();
    }
    $stmt_aluno->close();
}

// 2. Busca o número de ocorrências e de relatórios de desenvolvimento para os cards
$ocorrencias_count = 0;
$desenvolvimento_count = 0;
if ($id_aluno_logado > 0) {
    // Contagem de ocorrências
    $stmt_ocorrencias = $conexao->prepare("SELECT COUNT(*) as total FROM ocorrencias WHERE id_aluno = ?");
    $stmt_ocorrencias->bind_param("i", $id_aluno_logado);
    $stmt_ocorrencias->execute();
    $ocorrencias_count = $stmt_ocorrencias->get_result()->fetch_assoc()['total'];
    $stmt_ocorrencias->close();

    // Contagem de relatórios de desenvolvimento
    $stmt_dev = $conexao->prepare("SELECT COUNT(*) as total FROM desenvolvimento_observacoes WHERE id_aluno = ?");
    $stmt_dev->bind_param("i", $id_aluno_logado);
    $stmt_dev->execute();
    $desenvolvimento_count = $stmt_dev->get_result()->fetch_assoc()['total'];
    $stmt_dev->close();
}

// 3. Busca os 3 avisos mais recentes destinados ao público GERAL
$avisos_recentes = $conexao->query("
    SELECT titulo, descricao, data_aviso 
    FROM avisos 
    WHERE destinatario = 'GERAL' 
    ORDER BY data_aviso DESC 
    LIMIT 3
");
?>

<div class="summary-cards">
    <a href="perfil_crianca.php?aluno_id=<?php echo $id_aluno_logado; ?>" class="summary-card">
        <div class="card-icon blue"><i class="fas fa-child"></i></div>
        <div class="card-content">
            <h3>Perfil da Criança</h3>
            <p><?php echo htmlspecialchars($aluno_info['nome_completo'] ?? 'Ver dados'); ?></p>
        </div>
    </a>
    <a href="avisos_responsavel.php?aluno_id=<?php echo $id_aluno_logado; ?>" class="summary-card">
        <div class="card-icon green"><i class="fas fa-bell"></i></div>
        <div class="card-content"><h3>Avisos</h3><p>Ver comunicados</p></div>
    </a>
    <a href="atestados_responsavel.php?aluno_id=<?php echo $id_aluno_logado; ?>" class="summary-card">
        <div class="card-icon red"><i class="fas fa-notes-medical"></i></div>
        <div class="card-content"><h3>Atestados</h3><p>Enviar ou visualizar</p></div>
    </a>
    <a href="ocorrencias_responsavel.php?aluno_id=<?php echo $id_aluno_logado; ?>" class="summary-card">
        <div class="card-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="card-content"><h3>Ocorrências</h3><p><?php echo $ocorrencias_count; ?> registro(s)</p></div>
    </a>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-file-alt"></i><h3 class="section-title">Relatórios da Criança</h3></div>
    <div class="card-body">
         <div class="shortcut-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
             <a href="relatorio_frequencia.php?aluno_id=<?php echo $id_aluno_logado; ?>" class="shortcut-card" style="text-decoration: none; color: inherit; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <i class="fas fa-calendar-check fa-2x" style="color: #3498db;"></i>
                <p style="margin-top: 10px;">Frequência</p>
            </a>
            <a href="relatorio_rotina.php?aluno_id=<?php echo $id_aluno_logado; ?>" class="shortcut-card" style="text-decoration: none; color: inherit; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <i class="fas fa-clipboard-list fa-2x" style="color: #2ecc71;"></i>
                <p style="margin-top: 10px;">Rotina Diária</p>
            </a>
            <a href="relatorios_responsavel.php?aluno_id=<?php echo $id_aluno_logado; ?>" class="shortcut-card" style="text-decoration: none; color: inherit; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <i class="fas fa-chart-line fa-2x" style="color: #9b59b6;"></i>
                <p style="margin-top: 10px;">Desenvolvimento</p>
            </a>
        </div>
    </div>
</div>

<?php require_once 'templates/footer_responsavel.php'; ?>