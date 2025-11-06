<?php
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

echo json_encode([
    'success' => true,
    'message' => 'PHP is working!',
    'method' => $method,
    'timestamp' => date('Y-m-d H:i:s'),
    'post_data' => file_get_contents('php://input'),
    'get_params' => $_GET,
    'server_info' => [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown'
    ]
]);
?>
