<?php
$host = 'dpg-d4d5scal19vc73cbpd50-a.oregon-postgres.render.com';
$db   = 'meuprojetodb';
$user = 'meuprojetodb_user';
$pass = 'ARG3AoSXIauNk31ENsEeaMd4hJVZE0pz';
$port = '5432';

// DSN com SSL obrigatório, sem validação extra
$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "<h1>✅ Conexão com PostgreSQL 18 estabelecida!</h1>";
} catch (PDOException $e) {
    echo "<strong>❌ Erro ao conectar:</strong><br>";
    echo nl2br($e->getMessage());
}
?>
