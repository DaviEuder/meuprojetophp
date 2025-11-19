<?php
// Define o cabeçalho para exibir o resultado no navegador
header('Content-Type: text/html; charset=utf-8'); 

// ----------------------------------------------------
// FUNÇÃO DE CONEXÃO ROBUSTA 
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

    $db = new PDO($dsn, $user, $password); 
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    return $db;
}

// ----------------------------------------------------
// INÍCIO DA LIMPEZA POR PONTUAÇÃO
// ----------------------------------------------------

// Define o LIMITE DE PONTUAÇÃO. Tudo acima disso será deletado.
$limite_pontos = 500; 
$nome_tabela = 'registros_partida';

try {
    $conn = connectDB();
    
    // Comando DELETE SQL: Deleta registros onde a pontuação é maior que o limite
    $stmt = $conn->prepare("
        DELETE FROM {$nome_tabela}
        WHERE pontos > :limite_pontos
    ");

    $stmt->bindParam(":limite_pontos", $limite_pontos, PDO::PARAM_INT);
    $stmt->execute();
    
    $linhas_afetadas = $stmt->rowCount();

    echo "<h1>Limpeza de Dados de Teste Concluída</h1>";
    echo "<p>Comando executado: DELETE FROM {$nome_tabela} WHERE pontos > {$limite_pontos}</p>";
    echo "<h2>✅ Sucesso! {$linhas_afetadas} registro(s) de teste (acima de {$limite_pontos} pontos) foram deletados.</h2>";
    echo "<p>Atualize seu placar principal para ver o resultado.</p>";


} catch (Exception $e) { 
    error_log("Erro na limpeza: " . $e->getMessage());
    http_response_code(500); 
    echo "<h1>❌ ERRO</h1>";
    echo "<p>Ocorreu um erro ao tentar deletar os registros:</p>";
    echo "<p>Detalhe: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
