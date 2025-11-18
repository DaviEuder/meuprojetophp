<?php
$host = "dpg-d4d5scal19vc73cbpd50-a.oregon-postgres.render.com";
$db   = "meuprojetodb";
$user = "meuprojetodb_user";
$pass = "ARG3AoSXIauNk31ENsEeaMd4hJVZE0pz";

$dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=verify-full";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✔ CONECTOU!";
} catch (Exception $e) {
    echo "❌ Falhou:<br><pre>" . $e->getMessage() . "</pre>";
}
