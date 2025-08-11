<?php
// Conexão com o banco
$host = "localhost";
$dbname = "sua_base_de_dados";
$user = "seu_usuario";
$pass = "sua_senha";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Pegando dados do POST
$nome       = $_POST['nome'];
$rg         = $_POST['rg'];
$cpf        = $_POST['cpf'];
$nascimento = $_POST['nascimento'];
$telefone   = $_POST['telefone'];
$email      = $_POST['email'];
$cep        = $_POST['cep'];
$logradouro = $_POST['logradouro'];
$numero     = $_POST['numero'];
$estado     = $_POST['estado'];
$cidade     = $_POST['cidade'];
$bairro     = $_POST['bairro'];
$complemento = $_POST['complemento'];
$turma      = $_POST['turma'];

// Prepara o INSERT
$sql = "INSERT INTO alunos (nome, rg, cpf, nascimento, telefone, email, cep, logradouro, numero, estado, cidade, bairro, complemento, turma)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    $nome, $rg, $cpf, $nascimento, $telefone, $email,
    $cep, $logradouro, $numero, $estado, $cidade, $bairro, $complemento, $turma
]);

// Redirecionar após salvar
header("Location: Listagem_Alunos.html");
exit();
?>
