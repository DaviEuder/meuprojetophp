<?php
// ... (Código de CONEXÃO do Passo 1 deve vir aqui) ...

// O bloco try/catch da conexão deve ser mantido
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Não foi possível conectar ao banco de dados para exibir o placar."); 
}

// ----------------------------------------------------------------------


// CONSULTA SQL: Seleciona nome e pontos, ordenando pela maior pontuação.
$stmt = $pdo->query('SELECT nome_jogador, pontos FROM registros_partida ORDER BY pontos DESC');
$pontuacoes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placar de Basquete - Trabalho Escolar</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f9; }
        h1 { color: #0056b3; border-bottom: 2px solid #ccc; padding-bottom: 10px; }
        table { 
            width: 80%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background-color: white;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 15px; 
            text-align: left; 
        }
        th { 
            background-color: #007bff; 
            color: white; 
            font-weight: bold;
        }
        tr:nth-child(even) { 
            background-color: #f1f1f1; 
        }
        tr:hover {
            background-color: #e9e9e9;
        }
        .vazio { color: #777; font-style: italic; padding: 15px; background-color: #fff; border-radius: 5px; }
    </style>
</head>
<body>

    <h1>🏀 Placar da Partida de Basquete</h1>

    <?php if (count($pontuacoes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Nome do Jogador</th>
                    <th>Pontuação</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $posicao = 1;
                foreach ($pontuacoes as $linha): 
                ?>
                    <tr>
                        <td><?php echo $posicao++; ?></td>
                        <td><?php echo htmlspecialchars($linha['nome_jogador']); ?></td>
                        <td><strong><?php echo htmlspecialchars($linha['pontos']); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="vazio">Ainda não há pontuações registradas no banco de dados.</p>
    <?php endif; ?>

</body>
</html>
