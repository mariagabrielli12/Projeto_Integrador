<?php
// Exibe erros (desativar em produção)
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Conexão com o banco de dados (ajuste para o seu ambiente)
    $pdo = new PDO("mysql:host=localhost;dbname=nome_do_banco", "usuario", "senha");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Coleta os dados do formulário
    $titulo = $_POST['titulo'];
    $categoria = $_POST['categoria'];
    $data_aviso = $_POST['data_aviso'];
    $conteudo = $_POST['conteudo'];

    // Insere no banco
    $sql = "INSERT INTO avisos (titulo, categoria, data_aviso, conteudo) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $categoria, $data_aviso, $conteudo]);

    // Redireciona para a listagem
    header("Location: avisos_secretario.html");
    exit();

} catch (PDOException $e) {
    echo "Erro ao salvar aviso: " . $e->getMessage();
}
?>
