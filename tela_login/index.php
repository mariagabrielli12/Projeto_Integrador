<?php
// Inicia a sessão no topo do ficheiro.
session_start();

// Se o utilizador já estiver logado, redireciona para a página de dashboard apropriada.
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $perfil = $_SESSION["perfil"];
    switch ($perfil) {
        case 'Diretor':
            // CORREÇÃO AQUI: Aponta para o novo dashboard do diretor
            header("location: ../visao_do_diretor/visao_diretor/index.php");
            break;
        case 'Secretario':
            header("location: ../visao_secretario/Telas_Secretario/index.php");
            break;
        case 'Professor':
            header("location: ../Visao_do_Professor/tela_principal_professor.php");
            break;
        case 'Bercarista':
            header("location: ../visao_bercarista/index.php"); // Corrigido para o novo index.php
            break;
        case 'Responsavel':
            header("location: ../Visao_do_responsavel/index.php");
            break;
    }
    exit;
}

// Inclui o ficheiro de conexão.
require_once '../conexao.php';

$login_err = "";

// Processa os dados do formulário quando ele é enviado.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $matricula = trim($_POST['matricula']);
    $senha = $_POST['senha'];

    $sql = "SELECT id_usuario, nome_completo, matricula, senha_hash, id_tipo FROM usuarios WHERE matricula = ?";

    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("s", $matricula);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id_usuario, $nome_completo, $matricula_db, $senha_hash_db, $id_tipo);
                if ($stmt->fetch()) {
                    
                    if (password_verify($senha, $senha_hash_db)) {
                        
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id_usuario"] = $id_usuario;
                        $_SESSION["nome_completo"] = $nome_completo;
                        $_SESSION["matricula"] = $matricula_db;
                        
                        $stmt_tipo = $conexao->prepare("SELECT nome_tipo FROM tipos_usuario WHERE id_tipo = ?");
                        $stmt_tipo->bind_param("i", $id_tipo);
                        $stmt_tipo->execute();
                        $result_tipo = $stmt_tipo->get_result();
                        $perfil = $result_tipo->fetch_assoc()['nome_tipo'];
                        $stmt_tipo->close();

                        $_SESSION["perfil"] = $perfil;

                        // ***** LÓGICA DE REDIRECIONAMENTO APÓS LOGIN *****
                        switch ($perfil) {
                            case 'Diretor':
                                // CORREÇÃO AQUI TAMBÉM
                                header("location: ../visao_do_diretor/index.php");
                                break;
                            case 'Secretario':
                                header("location: ../visao_secretario/Telas_Secretario/index.php");
                                break;
                            case 'Professor':
                                header("location: ../Visao_do_Professor/tela_principal_professor.php");
                                break;
                            case 'Bercarista':
                                header("location: ../visao_bercarista/index.php"); // Corrigido para o novo index.php
                                break;
                            case 'Responsavel':
                                header("location: ../Visao_do_responsavel/index.php");
                                break;
                            default:
                                $login_err = "Perfil de utilizador desconhecido.";
                                break;
                        }
                        exit;
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
                <label for="matricula"><i class="fas fa-id-card"></i> Matrícula ou Utilizador</label>
                <input type="text" id="matricula" name="matricula" placeholder="Digite sua matrícula ou utilizador" required>
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