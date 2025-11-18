<?php
$host = "dpg-d4d5scal19vc73cbpd50-a.oregon-postgres.render.com";
$db   = "meuprojetodb";
$user = "meuprojetodb_user";
$pass = "ARG3AoSXIauNk31ENsEeaMd4hJVZE0pz";
$port = "5432";

$connString = "host=$host port=$port dbname=$db user=$user password=$pass sslmode=require sslrootcert=system";
$conn = pg_connect($connString);

if ($conn) {
    echo "<h1>🏀 Conexão estabelecida com PostgreSQL 18!</h1>";
    $result = pg_query($conn, "SELECT * FROM registros_partida ORDER BY pontos DESC");
    while ($row = pg_fetch_assoc($result)) {
        echo "{$row['nome_jogador']} - {$row['pontos']} pontos<br>";
    }
} else {
    echo "❌ Erro ao conectar (Opção 1).";
}
?>
