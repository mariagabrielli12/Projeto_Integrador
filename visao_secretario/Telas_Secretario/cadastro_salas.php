<?php
// cadastro_salas.php

// Configurações do banco de dados (ajuste conforme seu ambiente)
$host = 'localhost';
$db   = 'rede_educacional';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Conexão com PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Exceções em erros
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Verifica se o método é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recebe e sanitiza os dados do formulário
    $room_number       = trim($_POST['room_number'] ?? '');
    $room_type         = trim($_POST['room_type'] ?? '');
    $room_capacity     = intval($_POST['room_capacity'] ?? 0);
    $room_status       = trim($_POST['room_status'] ?? '');
    $last_maintenance  = $_POST['last_maintenance'] ?? null;
    $next_maintenance  = $_POST['next_maintenance'] ?? null;
    $maintenance_notes = trim($_POST['maintenance_notes'] ?? '');

    // Validações simples (pode ser expandida)
    if (empty($room_number) || empty($room_type) || $room_capacity < 1 || empty($room_status)) {
        die("Por favor, preencha todos os campos obrigatórios corretamente.");
    }

    // Prepara a query para inserir no banco
    $sql = "INSERT INTO salas (numero, tipo, capacidade, status, ultima_manutencao, proxima_manutencao, observacoes)
            VALUES (:numero, :tipo, :capacidade, :status, :ultima_manutencao, :proxima_manutencao, :observacoes)";

    $stmt = $pdo->prepare($sql);

    // Ajusta datas para null se vazias
    $last_maintenance = $last_maintenance ?: null;
    $next_maintenance = $next_maintenance ?: null;

    try {
        $stmt->execute([
            ':numero'            => $room_number,
            ':tipo'              => $room_type,
            ':capacidade'        => $room_capacity,
            ':status'            => $room_status,
            ':ultima_manutencao' => $last_maintenance,
            ':proxima_manutencao'=> $next_maintenance,
            ':observacoes'       => $maintenance_notes
        ]);

        echo "<p>Sala cadastrada com sucesso!</p>";
        echo '<p><a href="Listagem_Sala.html">Voltar para o cadastro</a></p>';
    } catch (PDOException $e) {
        echo "Erro ao cadastrar sala: " . $e->getMessage();
    }

} else {
    // Se não for POST, redireciona para o formulário
    header("Location: Listagem_Sala.html");
    exit();
}
