<?php
// Exibe erros durante o desenvolvimento (remover em produção)
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Conexão com o banco (ajuste as credenciais)
    $pdo = new PDO("mysql:host=localhost;dbname=nome_do_seu_banco", "usuario", "senha");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Captura dos dados do formulário
    $aluno = $_POST['aluno'];
    $matricula = $_POST['matricula'];
    $data_atestado = $_POST['data_atestado'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $motivo = $_POST['motivo'];

    // Inserção no banco
    $sql = "INSERT INTO atestados (aluno, matricula, data_atestado, data_inicio, data_fim, motivo)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$aluno, $matricula, $data_atestado, $data_inicio, $data_fim, $motivo]);

    // Redirecionamento após salvar
    header("Location: Listagem_Atestado.html");
    exit();

} catch (PDOException $e) {
    echo "Erro ao salvar atestado: " . $e->getMessage();
}
?>
