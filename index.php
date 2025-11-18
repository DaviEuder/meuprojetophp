<?php
// Configurações do banco
$host = "dpg-d4d5scal19vc73cbpd50-a.oregon-postgres.render.com";
$db   = "meuprojetodb";
$user = "meuprojetodb_user";
$pass = "ARG3AoSXIauNk31ENsEeaMd4hJVZE0pz";
$port = "5432";

// Caminhos de certificados
$systemCa = "/etc/ssl/certs/ca-certificates.crt";
$pgHome   = "/var/www/.postgresql";
$pgRoot   = $pgHome . "/root.crt";

// 1) Garantir que o bundle de certificados do sistema existe
$notes = [];
if (!file_exists($systemCa)) {
    $notes[] = "Bundle de CA não encontrado em $systemCa. Instale 'ca-certificates' no container.";
}

// 2) Preparar ~/.postgresql/root.crt (libpq usa isso por padrão)
if (!is_dir($pgHome)) {
    @mkdir($pgHome, 0700, true);
}
if (file_exists($systemCa) && !file_exists($pgRoot)) {
    @copy($systemCa, $pgRoot);
    @chmod($pgRoot, 0600);
    $notes[] = "Copiado CA do sistema para $pgRoot para uso padrão do libpq.";
}

// 3) Definir variáveis de ambiente para libpq (caso o cliente use)
if (file_exists($pgRoot)) {
    putenv("PGSSLROOTCERT=" . $pgRoot);
    putenv("PGSSLMODE=verify-full");
}

// Lista de tentativas de conexão (em ordem)
$attempts = [
    [
        "label" => "A1: verify-full + sslrootcert=system",
        "conn"  => "host=$host port=$port dbname=$db user=$user password=$pass sslmode=verify-full sslrootcert=system"
    ],
    [
        "label" => "A2: verify-full + sslrootcert caminho explícito",
        "conn"  => "host=$host port=$port dbname=$db user=$user password=$pass sslmode=verify-full sslrootcert=$systemCa"
    ],
    [
        "label" => "A3: verify-full sem sslrootcert (usando ~/.postgresql/root.crt)",
        "conn"  => "host=$host port=$port dbname=$db user=$user password=$pass sslmode=verify-full"
    ],
    [
        "label" => "A4: verify-full + TLS mínimo 1.2",
        "conn"  => "host=$host port=$port dbname=$db user=$user password=$pass sslmode=verify-full options='-c ssl_min_protocol_version=TLSv1.2'"
    ],
    [
        "label" => "B1: require sem sslrootcert (alguns ambientes negociam)",
        "conn"  => "host=$host port=$port dbname=$db user=$user password=$pass sslmode=require"
    ],
];

// Função para tentar conexão com pg_connect
function try_pg_connect_sequence(array $attempts, array &$log) {
    foreach ($attempts as $a) {
        $label = $a["label"];
        $connString = $a["conn"];
        $conn = @pg_connect($connString);
        if ($conn) {
            $log[] = "Conexão bem-sucedida via: $label";
            return $conn;
        } else {
            $err = error_get_last();
            $msg = $err && isset($err['message']) ? $err['message'] : "Erro desconhecido";
            $log[] = "Falhou: $label -> $msg";
        }
    }
    return false;
}

$log = [];
$conn = try_pg_connect_sequence($attempts, $log);

// Fallback final com PDO (negociação diferente), usando env PGSSLROOTCERT se disponível
if (!$conn) {
    $log[] = "Tentando fallback com PDO (pdo_pgsql).";
    try {
        // Tentar primeiro com verify-full (libpq vai usar PGSSLROOTCERT)
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=verify-full";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $log[] = "Conexão PDO bem-sucedida em verify-full.";
        $conn = $pdo; // Usaremos PDO para consultas caso pg_connect falhe
    } catch (Throwable $e1) {
        $log[] = "PDO verify-full falhou: " . $e1->getMessage();
        try {
            // Tentar com require no PDO
            $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $log[] = "Conexão PDO bem-sucedida em require.";
            $conn = $pdo;
        } catch (Throwable $e2) {
            $log[] = "PDO require falhou: " . $e2->getMessage();
        }
    }
}

// HTML básico
echo "<h1>🏀 Projeto da cesta de basquete está no ar!</h1>";

// Mostrar notas e log de tentativa (útil para diagnóstico)
if (!empty($notes)) {
    echo "<p><strong>Notas:</strong><br>" . implode("<br>", array_map('htmlspecialchars', $notes)) . "</p>";
}
echo "<details><summary>Log de conexão</summary><pre>";
foreach ($log as $line) {
    echo htmlspecialchars($line) . "\n";
}
echo "</pre></details>";

if ($conn) {
    // Formulário para inserir registros
    echo '<h2>Registrar jogador</h2>
    <form method="POST">
        <input name="nome" placeholder="Nome do jogador" required>
        <input name="pontos" type="number" placeholder="Pontos" min="0" required>
        <button type="submit">Registrar</button>
    </form>';

    // Inserção (PDO ou pg)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome   = $_POST['nome'] ?? '';
        $pontos = (int)($_POST['pontos'] ?? 0);

        if ($nome !== '') {
            if ($conn instanceof PDO) {
                $stmt = $conn->prepare("INSERT INTO registros_partida (nome_jogador, pontos) VALUES (:nome, :pontos)");
                $ok = $stmt->execute([':nome' => $nome, ':pontos' => $pontos]);
            } else {
                $ok = pg_query_params($conn, "INSERT INTO registros_partida (nome_jogador, pontos) VALUES ($1, $2)", [$nome, $pontos]);
            }
            echo $ok ? "✅ Dados registrados com sucesso!<br>" : "❌ Erro ao registrar dados.<br>";
        } else {
            echo "❌ Nome vazio. Dados não registrados.<br>";
        }
    }

    // Consulta ranking (PDO ou pg)
    echo "<h2>📊 Ranking de jogadores</h2>
          <table border='1' cellpadding='5'>
          <tr><th>Posição</th><th>Jogador</th><th>Pontos</th><th>Data</th></tr>";

    if ($conn instanceof PDO) {
        $stmt = $conn->query("SELECT nome_jogador, pontos, data_registro FROM registros_partida ORDER BY pontos DESC");
        $posicao = 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$posicao}</td>
                    <td>".htmlspecialchars($row['nome_jogador'])."</td>
                    <td>".htmlspecialchars($row['pontos'])."</td>
                    <td>".htmlspecialchars($row['data_registro'])."</td>
                  </tr>";
            $posicao++;
        }
    } else {
        $result = pg_query($conn, "SELECT nome_jogador, pontos, data_registro FROM registros_partida ORDER BY pontos DESC");
        $posicao = 1;
        while ($row = pg_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$posicao}</td>
                    <td>".htmlspecialchars($row['nome_jogador'])."</td>
                    <td>".htmlspecialchars($row['pontos'])."</td>
                    <td>".htmlspecialchars($row['data_registro'])."</td>
                  </tr>";
            $posicao++;
        }
    }

    echo "</table>";
} else {
    echo "<strong>❌ Erro ao conectar ao banco:</strong><br>";
    echo "Não foi possível estabelecer conexão com nenhuma das opções (verify-full/system, verify-full/caminho, verify-full/default, TLS1.2, require e fallback PDO).";
    echo "<br>Verifique se o container tem 'ca-certificates' instalado e se o hostname do banco corresponde exatamente ao certificado.";
}
?>
