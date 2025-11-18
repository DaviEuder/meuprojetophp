<?php

$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    die("Erro: DATABASE_URL não encontrada.");
}

$dsn = str_replace("postgres://", "pgsql:", $databaseUrl);

try {
    $conn = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $nome = trim($_POST['nome'] ?? '');
    $pontos = (int)($_POST['pontos'] ?? 0);

    if ($nome === '' || $pontos < 0) {
        echo "Erro: dados inválidos.";
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO registros_partida (nome_jogador, pontos)
        VALUES (:nome, :pontos)
    ");

    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":pontos", $pontos, PDO::PARAM_INT);
    $stmt->execute();

    echo "OK";

} catch (PDOException $e) {
    error_log("Erro ao inserir: " . $e->getMessage());
    echo "Erro no servidor.";
}
