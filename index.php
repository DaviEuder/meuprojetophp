<?php
$host = "dpg-d4d5scal19vc73cbpd50-a.oregon-postgres.render.com";
$db   = "meuprojetodb";
$user = "meuprojetodb_user";
$pass = "ARG3AoSXIauNk31ENsEeaMd4hJVZE0pz";
$port = "5432";

// Conexão usando pg_connect com fallback de sslmode
$connString = "host=$host port=$port dbname=$db user=$user password=$pass sslmode=require";
$conn = pg_connect($connString);

if (!$conn) {
    // Se falhar com require, tenta prefer
    $connString = "host=$host port=$port dbname=$db user=$user password=$pass sslmode=prefer";
    $conn = pg_connect($connString);
}

if (!$conn) {
    // Se ainda falhar, tenta sem SSL (disable)
    $connString = "host=$host port=$port dbname=$db user=$user password=$pass sslmode=disable";
    $conn = pg_connect($connString);
}

if ($conn) {
    echo "<h1>🏀 Projeto da cesta de basquete está no ar!</h1>";

    // Se recebeu dados via POST, insere no banco
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome   = $_POST['nome'] ?? '';
        $pontos = $_POST['pontos'] ?? 0;

        if (!empty($nome)) {
            $result = pg_query_params(
                $conn,
                "INSERT INTO registros_partida (nome_jogador, pontos) VALUES ($1, $2)",
                [$nome, $pontos]
            );
            if ($result) {
                echo "✅ Dados registrados com sucesso!<br>";
            } else {
                echo "❌ Erro ao registrar dados.<br>";
            }
        } else {
            echo "❌ Nome vazio. Dados não registrados.<br>";
        }
    }

    // Mostrar ranking
    $result = pg_query($conn, "SELECT * FROM registros_partida ORDER BY pontos DESC");
    echo "<h2>📊 Ranking de jogadores</h2>
          <table border='1' cellpadding='5'>
          <tr><th>Posição</th><th>Jogador</th><th>Pontos</th><th>Data</th></tr>";

    $posicao = 1;
    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>
                <td>{$posicao}</td>
                <td>{$row['nome_jogador']}</td>
                <td>{$row['pontos']}</td>
                <td>{$row['data_registro']}</td>
              </tr>";
        $posicao++;
    }

    echo "</table>";

} else {
    echo "<strong>❌ Erro ao conectar ao banco:</strong><br>";
    echo "Não foi possível estabelecer conexão nem com sslmode=require, prefer ou disable.";
}
?>
