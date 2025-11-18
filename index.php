<?php
// ======================================================================
// CONEXÃO COM O BANCO DE DADOS (USANDO A VARIAVEL DE AMBIENTE DO RENDER)
// ======================================================================
$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    die("Erro: Variavel de ambiente DATABASE_URL nao encontrada. Nao e possivel exibir o placar.");
}

$dsn = str_replace("postgres://", "pgsql:", $databaseUrl);

try {
     $pdo = new PDO($dsn, null, null, [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     ]);
} catch (\PDOException $e) {
     die("Nao foi possivel conectar ao banco de dados para exibir o placar: " . $e->getMessage()); 
}
// ----------------------------------------------------------------------


// CONSULTA SQL: Seleciona nome e pontos, ordenando pela maior pontuacao (pontos)
try {
    // Agrupamos por nome para somar as pontuacoes se o jogador for registrado varias vezes.
    // Se voce quer ver cada partida individualmente, remova o GROUP BY e SUM.
    $stmt = $pdo->query("
        SELECT nome_jogador, SUM(pontos) AS pontuacao_total
        FROM registros_partida 
        GROUP BY nome_jogador
        ORDER BY pontuacao_total DESC
    ");
    $pontuacoes = $stmt->fetchAll();
} catch (PDOException $e) {
    $pontuacoes = [];
    $erro_sql = "Erro ao buscar dados: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placar de Basquete - Visualizacao</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background-color: #e6e9f0; color: #333; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }
        h1 { color: #007bff; text-align: center; margin-bottom: 25px; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
            margin-top: 20px; 
            overflow: hidden;
            border-radius: 10px;
        }
        th, td { 
            padding: 15px; 
            text-align: left; 
        }
        th { 
            background-color: #007bff; 
            color: white; 
            font-weight: 600;
        }
        tr:nth-child(even) { 
            background-color: #f8f8f8; 
        }
        tr:hover {
            background-color: #e0f7fa;
            transition: background-color 0.3s;
        }
        td:nth-child(3) { font-weight: bold; color: #d9534f; } /* Destaca a pontuacao */
        .posicao-ouro { background-color: #ffcc00; font-weight: bold; color: #333 !important; }
        .vazio, .erro { text-align: center; padding: 20px; border: 1px solid #ffdddd; background-color: #fff0f0; border-radius: 8px; color: #d9534f; }
    </style>
</head>
<body>

    <div class="container">
        <h1>🏀 Placar da Partida de Basquete</h1>

        <?php if (isset($erro_sql)): ?>
            <p class="erro">⚠️ <?php echo htmlspecialchars($erro_sql); ?></p>
        <?php elseif (count($pontuacoes) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Posição</th>
                        <th>Nome do Jogador</th>
                        <th>Pontuação Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $posicao = 1;
                    foreach ($pontuacoes as $linha): 
                        $classe_posicao = ($posicao == 1) ? 'posicao-ouro' : '';
                    ?>
                        <tr class="<?php echo $classe_posicao; ?>">
                            <td><?php echo $posicao++; ?></td>
                            <td><?php echo htmlspecialchars($linha['nome_jogador']); ?></td>
                            <td><?php echo htmlspecialchars($linha['pontuacao_total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="vazio">Ainda não há pontuações registradas no banco de dados. Envie dados pelo script Python primeiro.</p>
        <?php endif; ?>
    </div>

</body>
</html>
