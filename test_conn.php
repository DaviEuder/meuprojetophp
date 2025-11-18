<?php
try {
    $host = getenv("PGHOST");
    $port = getenv("PGPORT");
    $db   = getenv("PGDATABASE");
    $user = getenv("PGUSER");
    $pass = getenv("PGPASSWORD");

    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    echo "✔ Conexão bem sucedida!";
} catch (PDOException $e) {
    echo "❌ Erro ao conectar ao banco<br>" . $e->getMessage();
}
