<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
$stmt = $pdo->query('SHOW DATABASES');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $db = $row['Database'];
    try {
        $tables = $pdo->query("SHOW TABLES IN `$db`");
        while ($r = $tables->fetch(PDO::FETCH_NUM)) {
            if (strpos(strtolower($r[0]), 'medicine') !== false) {
                echo "Found $db.{$r[0]}\n";
            }
        }
    } catch (Exception $e) {
    }
}
