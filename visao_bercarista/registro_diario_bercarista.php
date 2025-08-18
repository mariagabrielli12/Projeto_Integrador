<?php
session_start();
require_once '../conexao.php';

// Simulação de berçarista logado
$bercarista_logado_id = 4;
$nome_bercarista_logado = "Usuário Berçarista";

// --- LÓGICA DO BANCO DE DADOS ---
$turmas = $conexao->query("SELECT id, nome FROM turmas WHERE nome LIKE 'Berçário%' OR nome LIKE 'Maternal%' ORDER BY nome")->fetch_all(MYSQLI_ASSOC);
// --- FIM DA LÓGICA ---
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Berçarista - Registro Diário</title>
    <link rel="stylesheet" href="CSS_Bercarista/Style_bercarista.css"> 
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <h2><i data-lucide="school"></i> Berçário</h2>
            <nav>
                <ul>
                    <li class="active"><i data-lucide="baby"></i> <a href="registro_diario_bercarista.php">Avaliação do Dia</a></li>
                    <li><i data-lucide="log-out"></i> <a href="../tela_login/logout.php">Sair</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <span><?php echo htmlspecialchars($nome_bercarista_logado); ?></span>
                <i data-lucide="user-circle"></i>
            </header>

            <section class="painel">
                <h1>Painel de Avaliação Diária</h1>

                <?php
                if (isset($_SESSION['mensagem_sucesso'])) {
                    echo '<div class="alert success">' . htmlspecialchars($_SESSION['mensagem_sucesso']) . '</div>';
                    unset($_SESSION['mensagem_sucesso']);
                }
                if (isset($_SESSION['mensagem_erro'])) {
                    echo '<div class="alert error">' . htmlspecialchars($_SESSION['mensagem_erro']) . '</div>';
                    unset($_SESSION['mensagem_erro']);
                }
                ?>

                <main class="main-content2">
                    <section class="painel2">
                        <form action="processa_registro_diario.php" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="turma_id">Turma*</label>
                                    <select id="turma_id" name="turma_id" onchange="carregarAlunos(this.value)" required>
                                        <option value="">Selecione uma turma</option>
                                        <?php foreach($turmas as $turma): ?>
                                            <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="aluno_id">Criança*</label>
                                    <select id="aluno_id" name="aluno_id" required disabled>
                                        <option value="">Aguardando seleção de turma...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="avaliacao" id="avaliacoes">
                                <label for="sono">Momento do cochilo:</label>
                                <input type="text" name="registros[Sono]" placeholder="Ex: Dormiu por 1h30, acordou tranquilo(a)">
                                
                                <label for="alimentacao">Alimentação:</label>
                                <input type="text" name="registros[Alimentação]" placeholder="Ex: Aceitou bem a sopa de legumes e a fruta">
                                
                                <label for="higiene">Higiene:</label>
                                <input type="text" name="registros[Higiene]" placeholder="Ex: 3 trocas de fralda (xixi e cocô)">

                                <label for="observacao-geral">Observação Geral do Dia:</label>
                                <textarea name="registros[Observação]" rows="4" placeholder="Descreva aqui qualquer ponto relevante..."></textarea>
                            </div>
                            
                            <button type="submit">Salvar Avaliação</button>
                        </form>
                    </section>
                </main>
            </section>
        </main>
    </div>

    <script>
        lucide.createIcons();

        function carregarAlunos(turmaId) {
            const alunoSelect = document.getElementById("aluno_id");
            alunoSelect.innerHTML = '<option value="">Carregando...</option>';
            alunoSelect.disabled = true;

            if (!turmaId) {
                alunoSelect.innerHTML = '<option value="">Aguardando seleção de turma...</option>';
                return;
            }

            // Usamos fetch para chamar um script PHP que busca os alunos
            fetch("api_get_alunos.php?turma_id=" + turmaId)
                .then(response => response.json())
                .then(data => {
                    alunoSelect.innerHTML = '<option value="">Selecione uma criança</option>';
                    if (data.length > 0) {
                        data.forEach(aluno => {
                            const option = document.createElement("option");
                            option.value = aluno.id;
                            option.textContent = aluno.nome_completo;
                            alunoSelect.appendChild(option);
                        });
                        alunoSelect.disabled = false;
                    } else {
                        alunoSelect.innerHTML = '<option value="">Nenhuma criança nesta turma</option>';
                    }
                });
        }
    </script>
</body>
</html>