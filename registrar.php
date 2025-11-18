<?php
$host = "dpg-d4d5scali9vc73cbpd50-a.oregon-postgres.render.com";
$port = 5432;
$dbname = "meuprojetodb";
$user = "meuprojetodb_user";
$password = "ARG3AoSXIauNk3lENsEeaMd4hJVZEOpz";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $nome = trim($_POST["nome"] ?? "");
    $pontos = (int) ($_POST["pontos"] ?? 0);

    if ($nome === "") {
        echo "Erro: nome vazio.";
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO registros_partida (nome_jogador, pontos)
        VALUES (:nome, :pontos)
    ");

    $stmt->execute([
        ":nome" => $nome,
        ":pontos" => $pontos
    ]);

    echo "OK";

} catch (PDOException $e) {
    echo "Erro ao inserir no banco: " . htmlspecialchars($e->getMessage());
}
