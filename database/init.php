<?php
/**
 * Database Initialization Script
 * Run this file once to set up the database
 */

// Include configuration
require_once '../config/config.php';

// Database connection parameters
$host = DB_HOST;
$username = DB_USER;
$password = DB_PASS;
$dbname = DB_NAME;

try {
    // Connect to MySQL database (database should already exist)
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.<br>";
    
    // Read and execute schema-no-create-db.sql (without CREATE DATABASE)
    $sql = file_get_contents(__DIR__ . '/schema-no-create-db.sql');
    
    if ($sql === false) {
        throw new Exception("Could not read schema.sql file");
    }
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $conn->exec($statement);
        }
    }
    
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
