<?php
// Test what type of database Wasmer provides
$host = 'db.fr-pari1.bengt.wasmernet.com';
$port = 10272;
$dbname = 'db_dengue';
$username = 'c545581b7516800070e930ad2164';
$password = '0690c545-581b-767f-8000-8a9f555c671e';

echo "<h3>Testing Database Connection</h3>";

// Test MySQL
echo "<h4>Testing MySQL...</h4>";
try {
    $conn = new PDO("mysql:host=$host:$port;dbname=$dbname", $username, $password);
    echo "✓ MySQL connection successful!<br>";
    echo "Database type: MySQL<br>";
    $conn = null;
} catch(PDOException $e) {
    echo "✗ MySQL failed: " . $e->getMessage() . "<br>";
}

// Test PostgreSQL without SSL
echo "<h4>Testing PostgreSQL (no SSL)...</h4>";
try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;sslmode=disable", $username, $password);
    echo "✓ PostgreSQL connection successful!<br>";
    echo "Database type: PostgreSQL<br>";
    $conn = null;
} catch(PDOException $e) {
    echo "✗ PostgreSQL (no SSL) failed: " . $e->getMessage() . "<br>";
}

// Test PostgreSQL with SSL
echo "<h4>Testing PostgreSQL (with SSL)...</h4>";
try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require", $username, $password);
    echo "✓ PostgreSQL (SSL) connection successful!<br>";
    echo "Database type: PostgreSQL with SSL<br>";
    $conn = null;
} catch(PDOException $e) {
    echo "✗ PostgreSQL (SSL) failed: " . $e->getMessage() . "<br>";
}
?>
