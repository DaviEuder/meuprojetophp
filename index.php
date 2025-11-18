<?php
$host = 'dpg-d4d5scal19vc73cbpd50-a.oregon-postgres.render.com';
$db   = 'meuprojetodb';
$user = 'meuprojetodb_user';
$pass = 'ARG3AoSXIauNk31ENsEeaMd4hJVZE0pz';
$port = '5432';

// DSN com SSL obrigatório
$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=prefer";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Se recebeu dados via POST, insere no banco
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'] ?? '';
        $pontos = $_POST['pontos'] ?? 0;

        if (!empty($nome)) {
            $stmt = $pdo->prepare("INSERT INTO registros_partida (nome_jogador, pontos) VALUES (:nome, :pontos)");
            $stmt->execute([
                ':nome' => $nome,
                ':pontos' => $pontos
            ]);
            echo "✅ Dados registrados com sucesso!<br>";
        } else {
            echo "❌ Nome vazio. Dados não registrados.<br>";
        }
    }

    // Mensagem principal
    echo "<h1>🏀 Projeto da cesta de basquete está no ar!</h1>";

    // Mostrar ranking
    $stmt = $pdo->query("SELECT * FROM registros_partida ORDER BY pontos DESC");
    echo "<h2>📊 Ranking de jogadores</h2><table border='1' cellpadding='5'><tr><th>Posição</th><th>Jogador</th><th>Pontos</th><th>Data</th></tr>";

    $posicao = 1;
    while ($row = $stmt->fetch()) {
        echo "<tr><td>{$posicao}</td><td>{$row['nome_jogador']}</td><td>{$row['pontos']}</td><td>{$row['data_registro']}</td></tr>";
        $posicao++;
    }

    echo "</table>";

} catch (PDOException $e) {
    echo "Erro ao conectar ou consultar o banco: " . $e->getMessage();
}
?>

