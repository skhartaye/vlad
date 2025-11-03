<?php
/**
 * Case Reports API
 * Handles case report CRUD operations with geocoding
 */

// Include configuration first (it sets session ini settings)
require_once '../config/config.php';

// Start session AFTER config is loaded
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/Database.php';
require_once '../classes/CaseReport.php';
require_once '../includes/security.php';

// Set JSON header
header('Content-Type: application/json');

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize CaseReport object
$caseReport = new CaseReport($db);

// Response array
$response = ['success' => false];

// isAuthenticated function now comes from security.php

/**
 * Geocode address using Nominatim API
 * @param string $address Address to geocode
 * @return array|bool Coordinates array or false on failure
 */
function geocodeAddress($address) {
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'DiseaseTracker/1.0');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        
        if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
            return [
                'lat' => floatval($data[0]['lat']),
                'lng' => floatval($data[0]['lon'])
            ];
        }
    }
    
    return false;
}

/**
 * Handle POST requests (create)
 */
if ($method === 'POST') {
    
    switch ($action) {
        
        case 'create':
            // Check authentication
            if (!isAuthenticated()) {
                $response['message'] = 'Authentication required';
                http_response_code(401);
                echo json_encode($response);
                exit();
            }
            
            // Get JSON input
            $data = json_decode(file_get_contents("php://input"), true);
            
            // Validate required fields
            if (empty($data['disease_type']) || empty($data['address'])) {
                $response['message'] = 'Disease type and address are required';
                $response['errors'] = [];
                
                if (empty($data['disease_type'])) $response['errors']['disease_type'] = 'Disease type is required';
                if (empty($data['address'])) $response['errors']['address'] = 'Address is required';
                
                http_response_code(400);
                echo json_encode($response);
                exit();
            }
            
            // Validate disease type
            $validDiseases = ['dengue', 'leptospirosis', 'malaria'];
            if (!in_array(strtolower($data['disease_type']), $validDiseases)) {
                $response['message'] = 'Invalid disease type';
                $response['errors'] = ['disease_type' => 'Must be dengue, leptospirosis, or malaria'];
                http_response_code(400);
                echo json_encode($response);
                exit();
            }
            
            // Geocode address
            $coordinates = geocodeAddress($data['address']);
            
            if (!$coordinates) {
                $response['message'] = 'Could not geocode address. Please try a more specific location.';
                $response['errors'] = ['address' => 'Unable to find coordinates for this address'];
                http_response_code(400);
                echo json_encode($response);
                exit();
            }
            
            // Set case report properties
            $caseReport->user_id = $_SESSION['user_id'];
            $caseReport->disease_type = strtolower($data['disease_type']);
            $caseReport->address = $data['address'];
            $caseReport->latitude = $coordinates['lat'];
            $caseReport->longitude = $coordinates['lng'];
            
            // Create case report
            $caseId = $caseReport->create();
            
            if ($caseId) {
                $response['success'] = true;
                $response['message'] = 'Case report submitted successfully';
                $response['case_id'] = $caseId;
                $response['coordinates'] = $coordinates;
                http_response_code(201);
            } else {
                $response['message'] = 'Failed to create case report';
                http_response_code(500);
            }
            break;
            
        default:
            $response['message'] = 'Invalid action';
            http_response_code(400);
            break;
    }
    
}
/**
 * Handle GET requests (list)
 */
elseif ($method === 'GET') {
    
    switch ($action) {
        
        case 'list':
            $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
            $diseaseType = isset($_GET['disease_type']) ? $_GET['disease_type'] : null;
            $days = isset($_GET['days']) ? intval($_GET['days']) : null;
            
            $reports = [];
            
            // Filter by user (requires authentication)
            if ($userId !== null) {
                if (!isAuthenticated() || $_SESSION['user_id'] != $userId) {
                    $response['message'] = 'Unauthorized access';
                    http_response_code(403);
                    echo json_encode($response);
                    exit();
                }
                
                $caseReport->user_id = $userId;
                $reports = $caseReport->readByUser();
            }
            // Filter by disease type
            elseif ($diseaseType !== null) {
                $reports = $caseReport->readByDisease($diseaseType);
            }
            // Filter by recent days
            elseif ($days !== null) {
                $reports = $caseReport->readRecent($days);
            }
            // Get all reports
            else {
                $reports = $caseReport->read();
            }
            
            $response['success'] = true;
            $response['reports'] = $reports;
            $response['count'] = count($reports);
            http_response_code(200);
            break;
            
        default:
            $response['message'] = 'Invalid action';
            http_response_code(400);
            break;
    }
    
}
/**
 * Handle PUT requests (update)
 */
elseif ($method === 'PUT') {
    
    switch ($action) {
        
        case 'update':
            // Check authentication
            if (!isAuthenticated()) {
                $response['message'] = 'Authentication required';
                http_response_code(401);
                echo json_encode($response);
                exit();
            }
            
            // Get JSON input
            $data = json_decode(file_get_contents("php://input"), true);
            
            // Validate required fields
            if (empty($data['case_id']) || empty($data['address']) || empty($data['disease_type'])) {
                $response['message'] = 'Case ID, disease type, and address are required';
                http_response_code(400);
                echo json_encode($response);
                exit();
            }
            
            // Geocode new address
            $coordinates = geocodeAddress($data['address']);
            
            if (!$coordinates) {
                $response['message'] = 'Could not geocode address. Please try a more specific location.';
                http_response_code(400);
                echo json_encode($response);
                exit();
            }
            
            // Set case report properties
            $caseReport->id = $data['case_id'];
            $caseReport->user_id = $_SESSION['user_id'];
            $caseReport->disease_type = strtolower($data['disease_type']);
            $caseReport->address = $data['address'];
            $caseReport->latitude = $coordinates['lat'];
            $caseReport->longitude = $coordinates['lng'];
            
            // Update case report
            if ($caseReport->update()) {
                $response['success'] = true;
                $response['message'] = 'Case report updated successfully';
                $response['coordinates'] = $coordinates;
                http_response_code(200);
            } else {
                $response['message'] = 'Failed to update case report or unauthorized';
                http_response_code(500);
            }
            break;
            
        default:
            $response['message'] = 'Invalid action';
            http_response_code(400);
            break;
    }
    
}
/**
 * Handle DELETE requests (delete)
 */
elseif ($method === 'DELETE') {
    
    switch ($action) {
        
        case 'delete':
            // Check authentication
            if (!isAuthenticated()) {
                $response['message'] = 'Authentication required';
                http_response_code(401);
                echo json_encode($response);
                exit();
            }
            
            // Get JSON input
            $data = json_decode(file_get_contents("php://input"), true);
            
            // Validate required fields
            if (empty($data['case_id'])) {
                $response['message'] = 'Case ID is required';
                http_response_code(400);
                echo json_encode($response);
                exit();
            }
            
            // Set case report properties
            $caseReport->id = $data['case_id'];
            $caseReport->user_id = $_SESSION['user_id'];
            
            // Delete case report
            if ($caseReport->delete()) {
                $response['success'] = true;
                $response['message'] = 'Case report deleted successfully';
                http_response_code(200);
            } else {
                $response['message'] = 'Failed to delete case report or unauthorized';
                http_response_code(500);
            }
            break;
            
        default:
            $response['message'] = 'Invalid action';
            http_response_code(400);
            break;
    }
    
} else {
    $response['message'] = 'Method not allowed';
    http_response_code(405);
}

// Output response
echo json_encode($response);
?>
