<?php
// API Index - Vercel entry point
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Disease Tracker API',
    'version' => '1.0',
    'endpoints' => [
        'auth' => '/api/auth.php',
        'cases' => '/api/cases.php',
        'map-data' => '/api/map-data.php'
    ]
]);
?>
