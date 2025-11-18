<?php

// index.php - Código PHP de Conexão com Debugging

// ----------------------------------------------------
// CONEXÃO COM O BANCO DE DADOS (USANDO A VARIAVEL DE AMBIENTE DO RENDER)
// ----------------------------------------------------

date_default_timezone_set('America/Sao_Paulo');

$databaseUrl = getenv("DATABASE_URL");
$db = null;

// 1. Verifica se a variável de ambiente existe (O problema original!)
if (!$databaseUrl) {
    die("
        <h1>Erro Crítico de Configuração!</h1>
        <p>A variável de ambiente <code>DATABASE_URL</code> não foi encontrada.</p>
        <p><strong>Ação Necessária:</strong> Verifique se o seu arquivo <code>render.yaml</code> está configurado corretamente para vincular o serviço de banco de dados (Secret Reference) ao Web Service.</p>
    ");
}

// 2. Adapta a URL do PostgreSQL para o formato DSN do PDO (pgsql:...)
// Render usa 'postgres://', PHP PDO espera 'pgsql:'
$dsn = str_replace('postgres://', 'pgsql:', $databaseUrl);

// 3. Tenta estabelecer a conexão
try {
    // Cria a conexão PDO
    $db = new PDO($dsn);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ----------------------------------------------------
    // LÓGICA DO SEU APP: CRIAR TABELA E EXIBIR PLACAR
    // ----------------------------------------------------

    // Criar tabela se não existir
    $db->exec("
        CREATE TABLE IF NOT EXISTS placar (
            id SERIAL PRIMARY KEY,
            nome VARCHAR(50) NOT NULL,
            pontuacao INT NOT NULL DEFAULT 0,
            criado_em TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Inserir uma pontuação fictícia para teste
    $nome = "Teste_" . substr(md5(mt_rand()), 0, 5);
    $pontuacao = mt_rand(100, 1000);
    $stmt = $db->prepare("INSERT INTO placar (nome, pontuacao) VALUES (:nome, :pontuacao)");
    $stmt->execute([':nome' => $nome, ':pontuacao' => $pontuacao]);


    // Buscar e exibir o placar
    $placar = $db->query("SELECT nome, pontuacao FROM placar ORDER BY pontuacao DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

    echo "
        <h1>Conexão com o Banco de Dados OK!</h1>
        <p>Tudo configurado! O Blueprint vinculou o DB, o Docker instalou o <code>pdo_pgsql</code>, e o PHP conectou.</p>
        <h2>Placar (Exemplo)</h2>
        <table border='1' cellpadding='10'>
            <tr><th>Nome</th><th>Pontuação</th></tr>
    ";

    foreach ($placar as $registro) {
        echo "<tr><td>" . htmlspecialchars($registro['nome']) . "</td><td>" . htmlspecialchars($registro['pontuacao']) . "</td></tr>";
    }

    echo "</table>";


} catch (PDOException $e) {
    // 4. Captura e exibe erros de conexão ou SQL
    die("
        <h1>Erro de Conexão com o Banco de Dados!</h1>
        <p>O <code>DATABASE_URL</code> foi encontrado, mas a conexão falhou. Isso pode ser erro de senha/usuário/firewall.</p>
        <p><strong>Erro:</strong> " . $e->getMessage() . "</p>
    ");
}

$db = null;

?>
