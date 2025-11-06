<?php
/**
 * PostgreSQL Database Initialization Script
 * Run this file once to set up the database
 */

// Include configuration
require_once '../config/config.php';

// Database connection parameters
$host = DB_HOST;
$port = DB_PORT;
$username = DB_USER;
$password = DB_PASS;
$dbname = DB_NAME;

try {
    // Connect to PostgreSQL database
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to PostgreSQL database successfully.<br>";
    
    // Read and execute schema-postgresql.sql
    $sql = file_get_contents(__DIR__ . '/schema-postgresql.sql');
    
    if ($sql === false) {
        throw new Exception("Could not read schema-postgresql.sql file");
    }
    
    // Execute the SQL
    $conn->exec($sql);
    
    echo "Database schema created successfully.<br>";
    echo "Disease types initialized.<br>";
    echo "<br><strong>Setup complete!</strong><br>";
    echo "You can now use the Disease Tracker application.<br>";
    echo "<br><a href='../index.html'>Go to Application</a>";
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "<br>";
    echo "Please check your database configuration in config/config.php";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
