<?php
$host = "dpg-d4d5scal19vc73cbpd50-a.oregon-postgres.render.com";
$port = "5432";
$dbname = "meuprojetodb";
$user = "meuprojetodb_user";
$password = "ARG3AoSXIauNk3IEnsEeaMd4hJvZEOpsz";

try {
    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nome = $_POST['nome'] ?? '';
    $pontos = $_POST['pontos'] ?? 0;

    if (empty($nome)) {
        echo "Erro: nome vazio.";
        exit;
    }

    $sql = "INSERT INTO registros_partida (nome_jogador, pontos)
            VALUES (:nome, :pontos)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":pontos", $pontos, PDO::PARAM_INT);
    $stmt->execute();

    echo "OK";

} catch (PDOException $e) {
    echo "Erro ao inserir no banco: " . $e->getMessage();
}
