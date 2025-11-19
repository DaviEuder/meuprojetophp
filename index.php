<?php
// index.php - Código PHP de Conexão e Exibição do Placar

date_default_timezone_set('America/Sao_Paulo');

$databaseUrl = getenv("DATABASE_URL");
$db = null;

// Verifica a variável de ambiente (Resolve o erro principal do Render)
if (!$databaseUrl) {
    die("
        <h1>Erro Crítico de Configuração!</h1>
        <p>A variável de ambiente <code>DATABASE_URL</code> não foi encontrada.</p>
        <p><strong>Ação Necessária:</strong> Verifique se o seu arquivo <code>render.yaml</code> está configurado corretamente para vincular o serviço de banco de dados (Secret Reference) ao Web Service.</p>
    ");
}

// Adapta a URL do PostgreSQL para o formato DSN do PDO (pgsql:...)
$dsn = str_replace('postgres://', 'pgsql:', $databaseUrl);

try {
    // Tenta estabelecer a conexão
    $db = new PDO($dsn);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // CRIAÇÃO DA TABELA (Com a estrutura CORRETA: registros_partida)
    $db->exec("
        CREATE TABLE IF NOT EXISTS registros_partida (
            id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
            nome_jogador VARCHAR(100) NOT NULL,
            pontos INTEGER DEFAULT 0,
            data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // LÓGICA DO SEU APP: INSERIR DADOS (APENAS PARA TESTE) E EXIBIR

    // Exemplo: Inserir uma pontuação fictícia para teste na tabela correta
    $nome = "Teste_" . substr(md5(mt_rand()), 0, 5);
    $pontuacao = mt_rand(100, 1000);
    $stmt = $db->prepare("INSERT INTO registros_partida (nome_jogador, pontos) VALUES (:nome, :pontuacao)");
    $stmt->execute([':nome' => $nome, ':pontuacao' => $pontuacao]);


    // Buscar e exibir o placar (Usando as colunas corretas)
    $placar = $db->query("SELECT nome_jogador, pontos FROM registros_partida ORDER BY pontos DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

    echo "
        <h1>Conexão com o Banco de Dados OK!</h1>
        <p>O serviço web está conectado à tabela <code>registros_partida</code>.</p>
        <h2>Placar (Exemplo)</h2>
        <table border='1' cellpadding='10'>
            <tr><th>Nome do Jogador</th><th>Pontuação</th></tr>
    ";

    foreach ($placar as $registro) {
        // Exibe os dados usando as chaves corretas do banco: nome_jogador e pontos
        echo "<tr><td>" . htmlspecialchars($registro['nome_jogador']) . "</td><td>" . htmlspecialchars($registro['pontos']) . "</td></tr>";
    }

    echo "</table>";


} catch (PDOException $e) {
    // Captura e exibe erros de conexão ou SQL
    die("
        <h1>Erro de Conexão com o Banco de Dados!</h1>
        <p>O <code>DATABASE_URL</code> foi encontrado, mas a conexão falhou.</p>
        <p><strong>Erro:</strong> " . $e->getMessage() . "</p>
        <p>Verifique o status do seu serviço de banco de dados no Render.</p>
    ");
}

$db = null;
?>
