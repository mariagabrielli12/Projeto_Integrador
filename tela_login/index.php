<?php
// Inicia a sessão no topo do arquivo.
session_start();

// Se o usuário já estiver logado, redireciona para a página de dashboard apropriada.
// Isso evita que ele veja a tela de login novamente.
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Redireciona com base no perfil já salvo na sessão
    $perfil = $_SESSION["perfil"];
    switch ($perfil) {
        case 'diretor':
            header("location: ../visao_do_diretor/index.html");
            break;
        case 'secretario':
            header("location: ../visao_secretario/Telas_Secretario/index.html");
            break;
        case 'professor':
            header("location: ../Visao_do_Professor/tela_principal_professor.html");
            break;
        case 'bercarista':
            header("location: ../visao_bercarista/CSS/index_bercarista.html");
            break;
        case 'responsavel':
            header("location: ../Visao_do_responsavel/index.html");
            break;
    }
    exit;
}

// Inclui o arquivo de conexão.
require_once '../conexao.php';

$login_err = "";

// Processa os dados do formulário quando ele é enviado.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $matricula = $conexao->real_escape_string($_POST['matricula']);
    $senha = $_POST['senha'];

    // Prepara a consulta para a tabela unificada 'usuarios'.
    $sql = "SELECT id, nome, matricula, senha_hash, perfil FROM usuarios WHERE matricula = ?";

    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("s", $matricula);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // Vincula os resultados às variáveis.
                $stmt->bind_result($id, $nome, $matricula_db, $senha_hash_db, $perfil);
                if ($stmt->fetch()) {
                    // Verifica a senha.
                    if (password_verify($senha, $senha_hash_db)) {
                        // Senha correta, inicia a sessão.
                        session_start();

                        // Armazena dados na sessão.
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["nome"] = $nome;
                        $_SESSION["matricula"] = $matricula_db;
                        $_SESSION["perfil"] = $perfil;

                        // ***** A LÓGICA DE REDIRECIONAMENTO *****
                        // Verifica o perfil e redireciona para a página correta.
                        switch ($perfil) {
                            case 'diretor':
                                header("location: ../visao_do_diretor/index.html");
                                break;
                            case 'secretario':
                                header("location: ../visao_secretario/Telas_Secretario/index.html");
                                break;
                            case 'professor':
                                header("location: ../Visao_do_Professor/tela_principal_professor.html");
                                break;
                            case 'bercarista':
                                header("location: ../visao_bercarista/CSS/index_bercarista.html");
                                break;
                            case 'responsavel':
                                header("location: ../Visao_do_responsavel/index.html");
                                break;
                            default:
                                // Caso haja um perfil inválido no banco.
                                $login_err = "Perfil de usuário desconhecido.";
                                break;
                        }
                        exit; // Importante para parar a execução após o redirecionamento.
                    } else {
                        $login_err = "Matrícula ou senha inválida.";
                    }
                }
            } else {
                $login_err = "Matrícula ou senha inválida.";
            }
        } else {
            $login_err = "Ops! Algo deu errado. Por favor, tente novamente.";
        }
        $stmt->close();
    }
    $conexao->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mundo Mágico</title>
    <link rel="stylesheet" href="CSS_login/style_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-graduation-cap" style="font-size: 3em; color: white;"></i>
        </div>
        <h1>Mundo Mágico</h1>
        <h2>Sistema Educacional Unificado</h2>
        
        <?php
        if (!empty($login_err)) {
            echo '<div style="color: #ffcdd2; background-color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px;">' . htmlspecialchars($login_err) . '</div>';
        }
        ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group">
                <label for="matricula"><i class="fas fa-id-card"></i> Matrícula ou Usuário</label>
                <input type="text" id="matricula" name="matricula" placeholder="Digite sua matrícula ou usuário" required>
            </div>
            
            <div class="input-group">
                <label for="password"><i class="fas fa-lock"></i> Senha</label>
                <input type="password" id="password" name="senha" placeholder="Digite sua senha" required>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button> 
        </form>
        
        <div class="links" style="justify-content: center; margin-top: 20px;">
             <a href="esqueci-senha.html"><i class="fas fa-question-circle"></i> Esqueci minha senha</a>
        </div>
    </div>
</body>
</html>