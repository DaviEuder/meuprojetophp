<?php

// ----------------------------------------------------
// // FUNÇÃO DE CONEXÃO ROBUSTA
// ----------------------------------------------------

/**
 * Conecta ao banco de dados PostgreSQL usando a variável de ambiente DATABASE_URL.
 * Usa parse_url() para desmembrar a string de conexão do Render.
 * @return PDO A instância da conexão PDO.
 * @throws Exception Se a variável DATABASE_URL não estiver definida ou a conexão falhar.
 */
function connectDB() {
    $databaseUrl = getenv('DATABASE_URL');

    if (empty($databaseUrl)) {
        throw new Exception("DATABASE_URL não está definida.");
    }

    // 1. Desmembra a URL de conexão fornecida pelo Render.
    $url = parse_url($databaseUrl);

    // 2. Extrai cada componente
    $host = $url['host'];
    $port = $url['port'] ?? 5432;
    $user = $url['user'];
    $password = $url['pass'];
    $dbname = ltrim($url['path'], '/');

    // 3. Constrói o DSN no formato exato que o PDO espera
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    // 4. Tenta conectar
    $db = new PDO($dsn, $user, $password); // Passa user e password explicitamente
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    return $db;
}

// ----------------------------------------------------
// // INÍCIO DA APLICAÇÃO
// ----------------------------------------------------

date_default_timezone_set('America/Sao_Paulo');

$db = null;

try {
    // Tenta estabelecer a conexão usando a função robusta
    $db = connectDB(); 

    // ----------------------------------------------------
    // // CRIAÇÃO DA TABELA (COM A ESTRUTURA CORRETA)
    // ----------------------------------------------------
    
    // A tabela deve ser 'registros_partida' com colunas 'nome_jogador' e 'pontos'
    $db->exec("
        CREATE TABLE IF NOT EXISTS registros_partida (
            id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
            nome_jogador VARCHAR(100) NOT NULL,
            pontos INTEGER DEFAULT 0,
            data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // ----------------------------------------------------
    // // LÓGICA DO SEU APP: INSERIR DADOS (APENAS PARA TESTE) E EXIBIR
    // ----------------------------------------------------

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


} catch (Exception $e) { // Captura Exception, que inclui PDOException
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
