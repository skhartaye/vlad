<?php
/**
 * Map Data API
 * Provides public heat map data for visualization
 * No authentication required - guest access allowed
 */

// Include configuration and required classes
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/CaseReport.php';

// Set JSON header
header('Content-Type: application/json');

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize CaseReport object
$caseReport = new CaseReport($db);

// Get query parameters
$diseaseType = isset($_GET['disease_type']) ? $_GET['disease_type'] : null;
$days = isset($_GET['days']) ? intval($_GET['days']) : 90; // Default 90 days

// Validate days parameter
if ($days < 1 || $days > 365) {
    $days = 90;
}

// Response array
$response = ['success' => false];

try {
    $reports = [];
    
    // Filter by disease type if specified
    if ($diseaseType !== null && !empty($diseaseType)) {
        // Validate disease type
        $validDiseases = ['dengue', 'leptospirosis', 'malaria'];
        if (!in_array(strtolower($diseaseType), $validDiseases)) {
            $response['message'] = 'Invalid disease type';
            http_response_code(400);
            echo json_encode($response);
            exit();
        }
        
        // Get reports by disease type (still filtered by days in the query)
        $query = "SELECT cr.id, cr.latitude, cr.longitude, cr.created_at, 
                        dt.name as disease_type, dt.color_code
                 FROM case_reports cr
                 INNER JOIN disease_types dt ON cr.disease_type_id = dt.id
                 WHERE dt.name = :disease_type 
                 AND cr.created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                 ORDER BY cr.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':disease_type', $diseaseType);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        $reports = $stmt->fetchAll();
    } else {
        // Get all recent reports
        $reports = $caseReport->readRecent($days);
    }
    
    // Format data for heat map (remove sensitive user information)
    $mapData = [];
    foreach ($reports as $report) {
        $mapData[] = [
            'lat' => floatval($report['latitude']),
            'lng' => floatval($report['longitude']),
            'disease_type' => $report['disease_type'],
            'color_code' => $report['color_code'],
            'date' => $report['created_at']
        ];
    }
    
    $response['success'] = true;
    $response['data'] = $mapData;
    $response['count'] = count($mapData);
    $response['days'] = $days;
    $response['disease_type'] = $diseaseType;
    http_response_code(200);
    
} catch (PDOException $e) {
    error_log("Map Data API Error: " . $e->getMessage());
    $response['message'] = 'Failed to retrieve map data';
    http_response_code(500);
}

// Output response
echo json_encode($response);
?>
