<?php
/**
 * Authentication API
 * Handles user registration, login, logout, and session checking
 */

// Include configuration first (it sets session ini settings)
require_once '../config/config.php';

// Start session AFTER config is loaded
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../includes/security.php';
require_once '../includes/logger.php';

// Set JSON header
header('Content-Type: application/json');

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

// Response array
$response = ['success' => false];

/**
 * Handle POST requests (register, login, logout)
 */
if ($method === 'POST') {

    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    switch ($action) {

        case 'register':
            // Validate required fields
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                $response['message'] = 'All fields are required';
                $response['errors'] = [];

                if (empty($data['username']))
                    $response['errors']['username'] = 'Username is required';
                if (empty($data['email']))
                    $response['errors']['email'] = 'Email is required';
                if (empty($data['password']))
                    $response['errors']['password'] = 'Password is required';

                http_response_code(400);
                echo json_encode($response);
                exit();
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Invalid email format';
                $response['errors'] = ['email' => 'Please enter a valid email address'];
                http_response_code(400);
                echo json_encode($response);
                exit();
            }

            // Validate password length
            if (strlen($data['password']) < 8) {
                $response['message'] = 'Password must be at least 8 characters';
                $response['errors'] = ['password' => 'Password must be at least 8 characters'];
                http_response_code(400);
                echo json_encode($response);
                exit();
            }

            // Set user properties
            $user->username = $data['username'];
            $user->email = $data['email'];
            $user->password = $data['password'];

            // Check if username exists
            if ($user->usernameExists()) {
                $response['message'] = 'Username already exists';
                $response['errors'] = ['username' => 'This username is already taken'];
                http_response_code(409);
                echo json_encode($response);
                exit();
            }

            // Check if email exists
            if ($user->emailExists()) {
                $response['message'] = 'Email already exists';
                $response['errors'] = ['email' => 'This email is already registered'];
                http_response_code(409);
                echo json_encode($response);
                exit();
            }

            // Create user
            if ($user->create()) {
                $logger->logAuth('register', $user->username, true);
                $response['success'] = true;
                $response['message'] = 'Registration successful';
                $response['user_id'] = $user->id;
                http_response_code(201);
            } else {
                $logger->logAuth('register', $user->username, false);
                $response['message'] = 'Registration failed. Please try again.';
                http_response_code(500);
            }
            break;

        case 'login':
            // Validate required fields
            if (empty($data['username']) || empty($data['password'])) {
                $response['message'] = 'Username and password are required';
                http_response_code(400);
                echo json_encode($response);
                exit();
            }

            // Check rate limiting
            $identifier = $data['username'] . '_' . $_SERVER['REMOTE_ADDR'];
            if (!checkRateLimit($identifier)) {
                $remaining = getRateLimitRemaining($identifier);
                $minutes = ceil($remaining / 60);
                $response['message'] = "Too many login attempts. Please try again in {$minutes} minutes.";
                http_response_code(429);
                echo json_encode($response);
                exit();
            }

            // Set user properties
            $user->username = $data['username'];
            $user->password = $data['password'];

            // Attempt login
            $userData = $user->login();

            if ($userData) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Store user data in session
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['email'] = $userData['email'];
                $_SESSION['logged_in'] = true;
                $_SESSION['last_activity'] = time();

                $logger->logAuth('login', $user->username, true);

                $response['success'] = true;
                $response['message'] = 'Login successful';
                $response['user'] = [
                    'id' => $userData['id'],
                    'username' => $userData['username'],
                    'email' => $userData['email']
                ];
                http_response_code(200);
            } else {
                $logger->logAuth('login', $user->username, false);
                $response['message'] = 'Invalid credentials';
                http_response_code(401);
            }
            break;

        case 'logout':
            $username = $_SESSION['username'] ?? 'unknown';

            // Destroy session
            $_SESSION = [];

            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }

            session_destroy();

            $logger->logAuth('logout', $username, true);

            $response['success'] = true;
            $response['message'] = 'Logout successful';
            http_response_code(200);
            break;

        default:
            $response['message'] = 'Invalid action';
            http_response_code(400);
            break;
    }

}
/**
 * Handle GET requests (check session)
 */ elseif ($method === 'GET') {

    switch ($action) {

        case 'check':
            // Check if user is logged in
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

                // Check session timeout (30 minutes)
                if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
                    // Session expired
                    session_unset();
                    session_destroy();

                    $response['authenticated'] = false;
                    $response['message'] = 'Session expired';
                    http_response_code(401);
                } else {
                    // Update last activity time
                    $_SESSION['last_activity'] = time();

                    $response['success'] = true;
                    $response['authenticated'] = true;
                    $response['user'] = [
                        'id' => $_SESSION['user_id'],
                        'username' => $_SESSION['username'],
                        'email' => $_SESSION['email']
                    ];
                    http_response_code(200);
                }
            } else {
                $response['authenticated'] = false;
                $response['message'] = 'Not authenticated';
                http_response_code(401);
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