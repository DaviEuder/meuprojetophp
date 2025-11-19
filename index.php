<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placar de Líderes - Desafio do Basquete</title>
    <!-- Carrega Tailwind CSS para estilização moderna e responsiva -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Configuração da Fonte Inter e cores para o Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#1D4ED8', // Cor primária (azul)
                        'gold': '#FFD700',
                        'silver': '#C0C0C0',
                        'bronze': '#CD7F32',
                        'dark-bg': '#1F2937',
                    }
                }
            }
        }
    </script>
    <style>
        /* Estilo para garantir a fonte Inter e o fundo escuro */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0F172A; /* Azul escuro quase preto */
        }
        /* Classe de animação para os pódios */
        .podium-shine {
            animation: shine 2s infinite;
        }
        @keyframes shine {
            0% { box-shadow: 0 0 5px rgba(255, 255, 255, 0.2); }
            50% { box-shadow: 0 0 15px rgba(255, 255, 255, 0.5); }
            100% { box-shadow: 0 0 5px rgba(255, 255, 255, 0.2); }
        }
    </style>
</head>
<body class="text-white min-h-screen p-4 sm:p-8">

<?php

// ----------------------------------------------------
// PHP - FUNÇÕES DE CONEXÃO E LEITURA (APENAS LEITURA!)
// ----------------------------------------------------

function connectDB() {
    $databaseUrl = getenv('DATABASE_URL');
    if (empty($databaseUrl)) {
        throw new Exception("DATABASE_URL não está definida.");
    }
    $url = parse_url($databaseUrl);
    $host = $url['host'];
    $port = $url['port'] ?? 5432;
    $user = $url['user'];
    $password = $url['pass'];
    $dbname = ltrim($url['path'], '/');
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    // Configura o PDO para lidar com caracteres UTF-8
    $db = new PDO($dsn, $user, $password, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ]); 
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function getScoreboard($conn) {
    // Seleciona os 15 melhores scores. O nome do jogador é sanitizado para exibição.
    $stmt = $conn->prepare("
        SELECT nome_jogador, pontos 
        FROM registros_partida 
        ORDER BY pontos DESC, data_registro DESC 
        LIMIT 15
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$conn = null;
$error_message = null;
$scoreboard = [];

try {
    $conn = connectDB();
    $scoreboard = getScoreboard($conn);
    
    // ⚠️ ATENÇÃO: O BLOCO QUE INSERIA DADOS DE TESTE FOI REMOVIDO AQUI! ⚠️
    // Este arquivo agora APENAS LÊ o placar.

} catch (Exception $e) { 
    $error_message = "Erro ao conectar ou buscar dados: " . htmlspecialchars($e->getMessage());
}

?>

<!-- Título Principal -->
<div class="max-w-4xl mx-auto text-center mb-10">
    <h1 class="text-5xl font-extrabold text-white mb-2">Placar de Líderes</h1>
    <p class="text-xl text-indigo-400">Desafio de Basquete Arduino</p>
    <?php if ($error_message): ?>
        <p class="text-red-500 mt-4 p-3 bg-red-900/50 rounded-lg border border-red-700"><?php echo $error_message; ?></p>
    <?php else: ?>
        <p class="text-green-400 mt-4">Conexão com Banco de Dados OK.</p>
    <?php endif; ?>
</div>

<!-- Container do Placar -->
<div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-2xl p-4 sm:p-6">
    
    <?php if (empty($scoreboard)): ?>
        <div class="text-center py-10 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0h3m-3 0h-3m3 0V7a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 01-2-2zm0 0h6"></path></svg>
            <p class="text-xl font-semibold">Nenhum registro encontrado no placar.</p>
            <p>Seja o primeiro a pontuar!</p>
        </div>
    <?php else: ?>
        
        <!-- Pódio (Top 3) -->
        <div class="grid grid-cols-3 gap-4 mb-6 text-center">
            
            <?php 
                $top3 = array_slice($scoreboard, 0, 3);
                $remainingScores = array_slice($scoreboard, 3);
            ?>

            <!-- 2º Lugar -->
            <?php if (isset($top3[1])): ?>
            <div class="order-1 flex flex-col justify-end">
                <div class="bg-silver/80 text-gray-900 p-3 sm:p-5 rounded-t-lg shadow-lg hover:shadow-xl transition duration-300">
                    <p class="font-bold text-lg sm:text-2xl"><?php echo htmlspecialchars($top3[1]['pontos']); ?></p>
                    <p class="text-sm sm:text-base truncate"><?php echo htmlspecialchars($top3[1]['nome_jogador']); ?></p>
                </div>
                <div class="bg-silver h-16 sm:h-24 rounded-b-lg flex items-center justify-center font-bold text-3xl">🥈</div>
            </div>
            <?php endif; ?>

            <!-- 1º Lugar -->
            <?php if (isset($top3[0])): ?>
            <div class="order-0 flex flex-col justify-end">
                <div class="bg-gold text-gray-900 p-4 sm:p-6 rounded-t-lg shadow-2xl podium-shine">
                    <p class="font-black text-2xl sm:text-4xl leading-none"><?php echo htmlspecialchars($top3[0]['pontos']); ?></p>
                    <p class="text-base sm:text-xl truncate mt-1 font-semibold"><?php echo htmlspecialchars($top3[0]['nome_jogador']); ?></p>
                </div>
                <div class="bg-gold h-24 sm:h-36 rounded-b-lg flex items-center justify-center font-black text-4xl">🥇</div>
            </div>
            <?php endif; ?>

            <!-- 3º Lugar -->
            <?php if (isset($top3[2])): ?>
            <div class="order-2 flex flex-col justify-end">
                <div class="bg-bronze/80 text-gray-900 p-3 sm:p-5 rounded-t-lg shadow-lg hover:shadow-xl transition duration-300">
                    <p class="font-bold text-lg sm:text-2xl"><?php echo htmlspecialchars($top3[2]['pontos']); ?></p>
                    <p class="text-sm sm:text-base truncate"><?php echo htmlspecialchars($top3[2]['nome_jogador']); ?></p>
                </div>
                <div class="bg-bronze h-12 sm:h-16 rounded-b-lg flex items-center justify-center font-bold text-2xl">🥉</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tabela dos demais (4º em diante) -->
        <?php if (!empty($remainingScores)): ?>
        <h3 class="text-xl font-bold mt-8 mb-4 border-b border-gray-700 pb-2 text-gray-300">Outros Melhores Resultados</h3>
        <ul class="space-y-2">
            <?php $rank = 4; ?>
            <?php foreach ($remainingScores as $row): ?>
                <li class="flex justify-between items-center bg-gray-700/50 p-3 rounded-lg hover:bg-gray-700 transition duration-150">
                    <span class="text-gray-400 font-semibold w-1/12"><?php echo $rank++; ?>.</span>
                    <span class="truncate w-6/12 font-medium"><?php echo htmlspecialchars($row['nome_jogador']); ?></span>
                    <span class="text-lg font-bold w-4/12 text-right text-indigo-300"><?php echo htmlspecialchars($row['pontos']); ?> pontos</span>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

    <?php endif; ?>

</div>

</body>
</html>
