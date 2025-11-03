# Design Document

## Overview

The Disease Heat Map Tracker is a full-stack web application built with PHP, MySQL, and JavaScript. The system integrates with the existing frontend (index.html) and adds a robust backend to handle user authentication, case report management, and heat map data visualization. The application uses XAMPP as the local development environment, providing Apache web server and MySQL database services.

The architecture follows a traditional MVC-inspired pattern with PHP handling server-side logic, MySQL for data persistence, and JavaScript for client-side interactivity and map visualization using Leaflet.js.

## Architecture

### Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Mapping Library**: Leaflet.js with Leaflet Control Geocoder
- **Backend**: PHP 7.4+ (XAMPP)
- **Database**: MySQL 5.7+ (XAMPP)
- **Web Server**: Apache 2.4+ (XAMPP)
- **Session Management**: PHP Sessions
- **Password Security**: PHP password_hash() with bcrypt

### System Architecture Diagram

\`\`\`mermaid
graph TB
    subgraph "Client Layer"
        A[Web Browser]
        B[Leaflet Map]
        C[JavaScript Client Logic]
    end
    
    subgraph "Application Layer"
        D[PHP Controllers]
        E[Authentication Module]
        F[Case Report Module]
        G[Geocoding Service]
    end
    
    subgraph "Data Layer"
        H[(MySQL Database)]
        I[Users Table]
        J[Case Reports Table]
        K[Disease Types Table]
    end
    
    A --> C
    C --> B
    C --> D
    D --> E
    D --> F
    D --> G
    E --> H
    F --> H
    H --> I
    H --> J
    H --> K
\`\`\`

### Directory Structure

\`\`\`
/disease-tracker/
├── index.html                 # Main frontend (existing)
├── config/
│   └── config.php            # Database configuration
├── includes/
│   ├── header.php            # Common header
│   ├── navbar.php            # Navigation bar
│   └── footer.php            # Common footer
├── api/
│   ├── auth.php              # Authentication endpoints
│   ├── cases.php             # Case report CRUD endpoints
│   └── map-data.php          # Heat map data endpoint
├── classes/
│   ├── Database.php          # Database connection class
│   ├── User.php              # User model and operations
│   └── CaseReport.php        # Case report model and operations
├── js/
│   ├── script.js             # Main JavaScript logic
│   ├── auth.js               # Authentication client logic
│   ├── map.js                # Map and heat map logic
│   └── reports.js            # Report submission logic
├── assets/
│   └── css/
│       └── style.css         # Additional styles
├── images/
│   └── logo.php              # Logo/branding
└── database/
    └── schema.sql            # Database schema
\`\`\`

## Components and Interfaces

### 1. Database Layer

#### Database Connection (classes/Database.php)

\`\`\`php
class Database {
    private $host = "localhost";
    private $db_name = "disease_tracker";
    private $username = "root";
    private $password = "";
    private $conn;
    
    public function getConnection() {
        // Returns PDO connection with error handling
    }
}
\`\`\`

#### User Model (classes/User.php)

\`\`\`php
class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $created_at;
    
    public function create() { }          // Create new user
    public function login() { }           // Authenticate user
    public function emailExists() { }     // Check email uniqueness
    public function usernameExists() { }  // Check username uniqueness
}
\`\`\`

#### Case Report Model (classes/CaseReport.php)

\`\`\`php
class CaseReport {
    private $conn;
    private $table_name = "case_reports";
    
    public $id;
    public $user_id;
    public $disease_type;
    public $address;
    public $latitude;
    public $longitude;
    public $created_at;
    
    public function create() { }              // Create case report
    public function read() { }                // Get all reports (for heat map)
    public function readByUser() { }          // Get user's reports
    public function update() { }              // Update report
    public function delete() { }              // Delete report
    public function readRecent($days) { }     // Get reports from last N days
    public function readByDisease($type) { }  // Filter by disease type
}
\`\`\`

### 2. API Endpoints

#### Authentication API (api/auth.php)

**POST /api/auth.php?action=register**
- Request: `{ username, email, password, disease_type }`
- Response: `{ success: true, message: "Registration successful" }`
- Validates input, hashes password, creates user record

**POST /api/auth.php?action=login**
- Request: `{ username, password }`
- Response: `{ success: true, user: { id, username, email } }`
- Validates credentials, creates PHP session

**POST /api/auth.php?action=logout**
- Response: `{ success: true }`
- Destroys PHP session

**GET /api/auth.php?action=check**
- Response: `{ authenticated: true, user: {...} }`
- Checks if user has active session

#### Case Reports API (api/cases.php)

**POST /api/cases.php?action=create**
- Request: `{ disease_type, address }`
- Response: `{ success: true, case_id: 123, coordinates: {lat, lng} }`
- Geocodes address, stores case report
- Requires authentication

**GET /api/cases.php?action=list**
- Query params: `user_id` (optional), `disease_type` (optional), `days` (optional)
- Response: `{ success: true, reports: [...] }`
- Returns filtered case reports

**PUT /api/cases.php?action=update**
- Request: `{ case_id, address, disease_type }`
- Response: `{ success: true }`
- Updates existing case report
- Requires authentication and ownership

**DELETE /api/cases.php?action=delete**
- Request: `{ case_id }`
- Response: `{ success: true }`
- Deletes case report
- Requires authentication and ownership

#### Map Data API (api/map-data.php)

**GET /api/map-data.php**
- Query params: `disease_type` (optional), `days` (default: 90)
- Response: `{ success: true, data: [{ lat, lng, disease_type, intensity }] }`
- Returns heat map data points
- Public endpoint (no authentication required)

### 3. Frontend Components

#### Authentication Module (js/auth.js)

\`\`\`javascript
class AuthManager {
    async register(userData) { }
    async login(credentials) { }
    async logout() { }
    async checkSession() { }
    isAuthenticated() { }
    getCurrentUser() { }
}
\`\`\`

#### Report Manager (js/reports.js)

\`\`\`javascript
class ReportManager {
    async submitReport(reportData) { }
    async getUserReports() { }
    async updateReport(caseId, data) { }
    async deleteReport(caseId) { }
    renderReportTable(reports) { }
}
\`\`\`

#### Map Manager (js/map.js)

\`\`\`javascript
class MapManager {
    initMap() { }
    async loadHeatMapData(filters) { }
    renderHeatMap(data) { }
    filterByDisease(diseaseType) { }
    addMarker(lat, lng, data) { }
    geocodeAddress(address) { }
}
\`\`\`

## Data Models

### Database Schema

#### Users Table

\`\`\`sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
\`\`\`

#### Disease Types Table

\`\`\`sql
CREATE TABLE disease_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    color_code VARCHAR(7) DEFAULT '#FF0000',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pre-populate with three diseases
INSERT INTO disease_types (name, description, color_code) VALUES
('dengue', 'Dengue fever transmitted by mosquitoes', '#d32f2f'),
('leptospirosis', 'Bacterial infection from contaminated water', '#f57c00'),
('malaria', 'Parasitic disease transmitted by mosquitoes', '#fbc02d');
\`\`\`

#### Case Reports Table

\`\`\`sql
CREATE TABLE case_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    disease_type_id INT NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (disease_type_id) REFERENCES disease_types(id),
    INDEX idx_user_id (user_id),
    INDEX idx_disease_type (disease_type_id),
    INDEX idx_created_at (created_at),
    INDEX idx_coordinates (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
\`\`\`

#### Sessions Table (Optional - for database-backed sessions)

\`\`\`sql
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    data TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
\`\`\`

### Data Flow Diagrams

#### User Registration Flow

\`\`\`mermaid
sequenceDiagram
    participant U as User
    participant F as Frontend
    participant A as API (auth.php)
    participant D as Database
    
    U->>F: Fill registration form
    F->>F: Validate input
    F->>A: POST /api/auth.php?action=register
    A->>A: Validate & sanitize data
    A->>A: Hash password (bcrypt)
    A->>D: INSERT INTO users
    D-->>A: User ID
    A-->>F: Success response
    F-->>U: Show success message
    F->>F: Redirect to login
\`\`\`

#### Case Report Submission Flow

\`\`\`mermaid
sequenceDiagram
    participant U as User
    participant F as Frontend
    participant A as API (cases.php)
    participant G as Geocoding Service
    participant D as Database
    
    U->>F: Submit address & disease
    F->>A: POST /api/cases.php?action=create
    A->>A: Check authentication
    A->>G: Geocode address
    G-->>A: Coordinates (lat, lng)
    A->>D: INSERT INTO case_reports
    D-->>A: Case ID
    A-->>F: Success + coordinates
    F->>F: Update report table
    F-->>U: Show confirmation
\`\`\`

#### Heat Map Loading Flow

\`\`\`mermaid
sequenceDiagram
    participant U as User
    participant F as Frontend
    participant M as Map Manager
    participant A as API (map-data.php)
    participant D as Database
    
    U->>F: Click "View Heat Map"
    F->>M: initMap()
    M->>M: Initialize Leaflet map
    M->>A: GET /api/map-data.php?days=90
    A->>D: SELECT case_reports (last 90 days)
    D-->>A: Report data with coordinates
    A-->>M: JSON array of points
    M->>M: Generate heat layer
    M-->>U: Display interactive heat map
\`\`\`

## Error Handling

### Backend Error Handling Strategy

1. **Database Errors**
   - Catch PDO exceptions
   - Log errors to PHP error log
   - Return generic error messages to client (avoid exposing DB structure)
   - Example: `{ success: false, message: "Database error occurred" }`

2. **Validation Errors**
   - Validate all input on server-side
   - Return specific validation messages
   - Example: `{ success: false, errors: { email: "Invalid email format" } }`

3. **Authentication Errors**
   - Use generic messages to prevent user enumeration
   - Example: "Invalid credentials" (don't specify if username or password is wrong)
   - Rate limiting for login attempts (future enhancement)

4. **Geocoding Errors**
   - Handle failed geocoding gracefully
   - Allow manual coordinate entry as fallback
   - Example: `{ success: false, message: "Could not geocode address. Please try a more specific location." }`

### Frontend Error Handling

1. **Network Errors**
   - Catch fetch() errors
   - Display user-friendly messages
   - Implement retry logic for transient failures

2. **Session Expiration**
   - Detect 401/403 responses
   - Redirect to login page
   - Preserve intended action for post-login redirect

3. **Form Validation**
   - Client-side validation before API calls
   - Real-time feedback on input fields
   - Prevent duplicate submissions

### Error Response Format

All API endpoints return consistent error format:

\`\`\`json
{
    "success": false,
    "message": "Human-readable error message",
    "errors": {
        "field_name": "Specific field error"
    },
    "code": "ERROR_CODE"
}
\`\`\`

## Security Considerations

### Authentication & Authorization

1. **Password Security**
   - Use `password_hash()` with PASSWORD_BCRYPT
   - Minimum 8 characters enforced
   - Never store plain text passwords

2. **Session Management**
   - Use PHP sessions with secure settings
   - Regenerate session ID on login
   - Set httpOnly and secure flags on session cookies
   - Implement session timeout (30 minutes)

3. **Authorization Checks**
   - Verify user owns resource before update/delete
   - Check authentication on all protected endpoints
   - Implement role-based access (future: admin role)

### Input Validation & Sanitization

1. **SQL Injection Prevention**
   - Use PDO prepared statements exclusively
   - Never concatenate user input into queries

2. **XSS Prevention**
   - Sanitize all output with `htmlspecialchars()`
   - Use Content-Security-Policy headers
   - Validate and escape JSON responses

3. **CSRF Protection**
   - Implement CSRF tokens for state-changing operations
   - Validate token on POST/PUT/DELETE requests

### Data Privacy

1. **Guest Access**
   - Heat map data is anonymized (no user info)
   - Only coordinates and disease type exposed

2. **User Data**
   - Email addresses not displayed publicly
   - Case reports linked to user ID only
   - Implement data retention policy (auto-delete old reports)

## Testing Strategy

### Unit Testing

1. **PHP Unit Tests (PHPUnit)**
   - Test User class methods (create, login, validation)
   - Test CaseReport class methods (CRUD operations)
   - Test Database connection handling
   - Mock database for isolated tests

2. **JavaScript Unit Tests (Jest)**
   - Test AuthManager methods
   - Test ReportManager methods
   - Test MapManager utility functions
   - Mock fetch() calls

### Integration Testing

1. **API Endpoint Tests**
   - Test complete request/response cycles
   - Verify authentication flows
   - Test error handling paths
   - Use test database

2. **Database Integration**
   - Test schema creation
   - Test foreign key constraints
   - Test transaction rollbacks
   - Verify indexes work correctly

### Manual Testing Checklist

1. **User Flows**
   - [ ] Register new account
   - [ ] Login with valid credentials
   - [ ] Login with invalid credentials
   - [ ] Submit case report with valid address
   - [ ] Submit case report with invalid address
   - [ ] View personal case reports
   - [ ] Update case report
   - [ ] Delete case report
   - [ ] Logout
   - [ ] Access as guest
   - [ ] View heat map as guest
   - [ ] Filter heat map by disease type

2. **Security Tests**
   - [ ] Attempt SQL injection in forms
   - [ ] Attempt XSS in text inputs
   - [ ] Access protected endpoints without auth
   - [ ] Attempt to modify another user's report
   - [ ] Verify password hashing in database

3. **Browser Compatibility**
   - [ ] Test on Chrome
   - [ ] Test on Firefox
   - [ ] Test on Edge
   - [ ] Test on mobile browsers

### Performance Testing

1. **Load Testing**
   - Test with 1000+ case reports in database
   - Measure heat map rendering time
   - Test concurrent user logins

2. **Database Optimization**
   - Verify indexes are used (EXPLAIN queries)
   - Optimize slow queries
   - Implement pagination for large result sets

## Geocoding Strategy

### Primary Approach: Nominatim (OpenStreetMap)

- Free, open-source geocoding service
- Already integrated via Leaflet Control Geocoder
- No API key required for moderate usage

### Implementation

\`\`\`php
function geocodeAddress($address) {
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'DiseaseTracker/1.0');
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (!empty($data)) {
        return [
            'lat' => $data[0]['lat'],
            'lng' => $data[0]['lon']
        ];
    }
    
    return false;
}
\`\`\`

### Fallback Options

1. **Manual Coordinate Entry**: Allow users to click on map to set location
2. **Cached Geocoding**: Store common addresses to reduce API calls
3. **Alternative Services**: Google Geocoding API (requires key) as backup

## Deployment Considerations

### XAMPP Setup

1. **Installation**
   - Install XAMPP for Windows
   - Start Apache and MySQL services
   - Access phpMyAdmin at http://localhost/phpmyadmin

2. **Database Setup**
   - Create database: `disease_tracker`
   - Import schema.sql
   - Configure user permissions

3. **PHP Configuration**
   - Enable required extensions: pdo_mysql, curl, mbstring
   - Set error reporting for development
   - Configure session settings in php.ini

4. **Virtual Host (Optional)**
   - Configure Apache virtual host for cleaner URLs
   - Enable mod_rewrite for URL routing

### File Permissions

- Ensure Apache can read all PHP files
- Restrict write access to upload directories only
- Set appropriate permissions on config files

### Environment Configuration

Create `config/config.php` with environment-specific settings:

\`\`\`php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'disease_tracker');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', 'http://localhost/disease-tracker/');
define('ENVIRONMENT', 'development'); // or 'production'

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
?>
\`\`\`

## Future Enhancements

1. **Admin Dashboard**: Manage users, view analytics, export data
2. **Email Notifications**: Alert users in high-risk areas
3. **Mobile App**: Native iOS/Android applications
4. **Advanced Analytics**: Trend analysis, prediction models
5. **Multi-language Support**: Localization for different regions
6. **Real-time Updates**: WebSocket for live heat map updates
7. **Data Export**: CSV/PDF reports for health authorities
8. **API Rate Limiting**: Prevent abuse of public endpoints
