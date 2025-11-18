<?php

// Tenta obter a variável DATABASE_URL do ambiente
$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    die("<h1>❌ Variável DATABASE_URL não encontrada</h1>");
}

// Converte postgres:// para pgsql:
$dsn = str_replace("postgres://", "pgsql:", $databaseUrl);

try {
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("<h1>❌ Erro ao conectar ao banco</h1><pre>" . $e->getMessage() . "</pre>");
}

echo "<h1>🏀 Projeto da cesta de basquete está no ar!</h1>";

// --- Registrar jogador ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $pontos = (int)($_POST['pontos'] ?? 0);

    if ($nome === '' || $pontos < 0) {
        echo "❌ Dados inválidos.<br>";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO registros_partida (nome_jogador, pontos)
            VALUES (:nome, :pontos)
        ");
        $stmt->execute([':nome' => $nome, ':pontos' => $pontos]);

        echo "✅ Dados registrados com sucesso!<br>";
    }
}

// --- Formulário ---
?>
<h2>Registrar jogador</h2>
<form method="POST">
    <input name="nome" placeholder="Nome do jogador" required>
    <input name="pontos" type="number" placeholder="Pontos" min="0" required>
    <button type="submit">Registrar</button>
</form>

<h2>📊 Ranking de jogadores</h2>
<table border="1" cellpadding="5">
<tr><th>Posição</th><th>Jogador</th><th>Pontos</th><th>Data</th></tr>

<?php
$stmt = $pdo->query("
    SELECT nome_jogador, pontos, data_registro
    FROM registros_partida
    ORDER BY pontos DESC, data_registro ASC
");

$posicao = 1;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$posicao}</td>
            <td>" . htmlspecialchars($row['nome_jogador']) . "</td>
            <td>" . htmlspecialchars($row['pontos']) . "</td>
            <td>" . htmlspecialchars($row['data_registro']) . "</td>
         </tr>";
    $posicao++;
}
?>
</table>

