<?php
header('Content-Type: text/plain'); // Retorna texto puro, ideal para API

// Usa a variável de ambiente fornecida pelo Render
$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    http_response_code(500);
    die("Erro: Variavel de ambiente DATABASE_URL nao encontrada.");
}

// Converte a URL padrão do Render (postgres://) para o formato PDO (pgsql:)
$dsn = str_replace("postgres://", "pgsql:", $databaseUrl);

try {
    // Tenta a conexão com o banco
    $conn = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Sanitiza e coleta os dados dos campos 'nome' e 'pontos' (esperados pelo Python)
    $nome = trim($_POST['nome'] ?? '');
    // Garante que 'pontos' seja um inteiro, se nao for enviado ou for invalido, sera 0
    $pontos = (int)($_POST['pontos'] ?? 0); 

    if ($nome === '' || $pontos < 0) {
        http_response_code(400); // Bad Request
        echo "Erro: Dados invalidos. Nome nao pode ser vazio.";
        exit;
    }

    // Prepara a query de insercao
    $stmt = $conn->prepare("
        INSERT INTO registros_partida (nome_jogador, pontos)
        VALUES (:nome, :pontos)
    ");

    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":pontos", $pontos, PDO::PARAM_INT);
    $stmt->execute();

    http_response_code(200); // OK
    echo "Registro inserido com sucesso!";

} catch (PDOException $e) {
    // Se ocorrer um erro no banco (ex: timeout, coluna inexistente)
    error_log("Erro ao inserir: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo "Erro no servidor ao processar o banco de dados.";
}
