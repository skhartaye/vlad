# Implementation Plan

- [x] 1. Set up database schema and configuration



  - Create database schema file with users, disease_types, and case_reports tables
  - Write database configuration file (config/config.php) with connection parameters
  - Create Database connection class (classes/Database.php) with PDO connection handling


  - _Requirements: 7.1, 7.2_

- [ ] 2. Implement User authentication system
  - Create User model class (classes/User.php) with properties and CRUD methods
  - Implement password hashing using bcrypt in User::create() method


  - Implement login validation in User::login() method with password verification
  - Add username and email uniqueness check methods
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 8.1, 8.2, 8.3_

- [ ] 3. Build authentication API endpoints
  - Create auth.php API file with register, login, logout, and check session actions
  - Implement registration endpoint with input validation and sanitization


  - Implement login endpoint with session creation and secure cookie settings
  - Implement logout endpoint with session destruction
  - Implement session check endpoint for frontend authentication state
  - Add error handling and consistent JSON response format
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 8.4_

- [ ] 4. Implement Case Report model and CRUD operations
  - Create CaseReport model class (classes/CaseReport.php) with properties
  - Implement create() method to insert new case reports


  - Implement read() method to retrieve all reports for heat map
  - Implement readByUser() method to get user-specific reports
  - Implement update() method with ownership verification
  - Implement delete() method with ownership verification
  - Implement readRecent() method to filter reports by date range
  - Implement readByDisease() method to filter by disease type
  - _Requirements: 2.4, 3.1, 3.2, 3.3, 3.4, 7.3, 7.4_


- [ ] 5. Create case reports API with geocoding
  - Create cases.php API file with create, list, update, delete actions
  - Implement geocoding function using Nominatim API with cURL
  - Implement create endpoint with address geocoding and coordinate storage
  - Implement list endpoint with filtering by user, disease type, and date range
  - Implement update endpoint with authentication and ownership checks
  - Implement delete endpoint with authentication and ownership checks
  - Add error handling for geocoding failures
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4_

- [x] 6. Build map data API for heat map visualization

  - Create map-data.php API endpoint for public heat map data access
  - Implement query to retrieve case reports from last 90 days
  - Add optional filtering by disease type
  - Format response as JSON array with coordinates and disease type
  - Ensure no sensitive user data is exposed in response
  - _Requirements: 4.2, 5.1, 5.2, 5.3, 5.4, 5.5, 6.1, 6.2_

- [x] 7. Refactor frontend authentication to use backend API


  - Create auth.js with AuthManager class for API communication
  - Replace localStorage user management with API-based authentication
  - Implement register() method calling backend registration endpoint
  - Implement login() method calling backend login endpoint with session handling
  - Implement logout() method calling backend logout endpoint
  - Implement checkSession() method to verify authentication state
  - Update index.html to use AuthManager instead of localStorage
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [x] 8. Integrate case report submission with backend


  - Create reports.js with ReportManager class for report operations
  - Implement submitReport() method calling backend create endpoint
  - Update submitDiseaseReport() function in index.html to use ReportManager
  - Implement getUserReports() method to fetch user's reports from backend
  - Implement updateReport() and deleteReport() methods
  - Update renderReports() to display backend data instead of localStorage
  - Add loading states and error handling for API calls
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4_

- [x] 9. Implement heat map with backend data integration


  - Create map.js with MapManager class for map operations
  - Refactor existing map initialization code into MapManager.initMap()
  - Implement loadHeatMapData() method calling map-data.php endpoint
  - Implement renderHeatMap() method to display data points on Leaflet map
  - Add heat map layer using Leaflet.heat plugin or marker clustering
  - Implement filterByDisease() method to filter heat map by disease type
  - Update toggleMap() function to load data from backend
  - _Requirements: 4.2, 5.1, 5.2, 5.3, 5.4, 5.5, 6.1, 6.3_

- [x] 10. Implement guest access functionality


  - Update navigation to show guest access option on home page
  - Modify authentication checks to allow guest users to view heat map
  - Restrict case submission forms to authenticated users only
  - Update API endpoints to allow unauthenticated access to map-data.php
  - Add UI indicators showing guest vs authenticated user status
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [x] 11. Add navigation and session management


  - Update includes/navbar.php with navigation links for all pages
  - Show different navigation options for authenticated vs guest users
  - Add logout button visible only to authenticated users
  - Implement session timeout handling on frontend
  - Add redirect to login page when session expires
  - Update includes/header.php and includes/footer.php for consistent layout
  - _Requirements: 9.1, 9.2, 9.3, 9.4_



- [ ] 12. Implement security measures
  - Add input sanitization using htmlspecialchars() on all output
  - Implement CSRF token generation and validation for state-changing requests
  - Add SQL injection prevention verification (ensure all queries use prepared statements)
  - Configure secure session settings in config.php
  - Add rate limiting for login attempts (basic implementation)
  - Implement authorization checks in all protected endpoints


  - _Requirements: 8.1, 8.2, 8.3, 8.4_

- [ ] 13. Add error handling and user feedback
  - Implement consistent error response format across all API endpoints
  - Add database error handling with logging in all model classes
  - Implement frontend error display for network failures
  - Add validation error messages for form inputs


  - Implement geocoding error handling with user-friendly messages
  - Add success notifications for user actions (report submitted, etc.)
  - _Requirements: 2.5, 7.5_

- [ ] 14. Implement data filtering and refresh functionality
  - Add disease type filter dropdown on heat map interface
  - Implement filter functionality to update heat map display

  - Add date range filter for viewing reports from specific time periods
  - Implement refresh button to reload heat map data
  - Update heat map automatically when new report is submitted
  - Remove deleted reports from heat map display
  - _Requirements: 5.4, 6.1, 6.2, 6.3, 6.4_

- [x] 15. Create database setup and deployment scripts

  - Write SQL script to create database and tables
  - Create PHP script to initialize database with disease types
  - Write setup instructions for XAMPP configuration
  - Document Apache virtual host configuration
  - Create environment configuration template
  - _Requirements: 7.1, 7.2_

- [x] 16. Add comprehensive error logging



  - Implement PHP error logging to file
  - Add JavaScript console logging for debugging
  - Create error log viewer for administrators
  - _Requirements: 7.5_
