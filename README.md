# Disease Heat Map Tracker

A web-based disease tracking system that monitors and visualizes the geographic distribution of three diseases: Dengue, Leptospirosis, and Malaria.

## Features

- **User Authentication**: Secure registration and login system with password hashing
- **Case Reporting**: Authenticated users can submit disease case reports with location
- **Heat Map Visualization**: Interactive map showing disease distribution
- **Guest Access**: View heat map without authentication
- **Data Filtering**: Filter by disease type and time range
- **Real-time Updates**: Automatic map refresh with new data

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Web Server**: Apache 2.4+ (XAMPP)
- **Mapping**: Leaflet.js with OpenStreetMap
- **Geocoding**: Nominatim API

## Installation

### Prerequisites

- XAMPP (or similar LAMP/WAMP stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Setup Instructions

1. **Install XAMPP**
   - Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Start Apache and MySQL services from XAMPP Control Panel

2. **Clone/Copy Project**
   ```bash
   # Copy project files to XAMPP htdocs directory
   # Example: C:\xampp\htdocs\disease-tracker\
   ```

3. **Configure Database**
   - Open `config/config.php`
   - Update database credentials if needed (default: root with no password)
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'disease_tracker');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Initialize Database**
   - Option 1: Run initialization script
     - Open browser and navigate to: `http://localhost/disease-tracker/database/init.php`
     - Follow on-screen instructions
   
   - Option 2: Manual setup via phpMyAdmin
     - Open phpMyAdmin: `http://localhost/phpmyadmin`
     - Create new database named `disease_tracker`
     - Import `database/schema.sql` file

5. **Verify Installation**
   - Open browser and navigate to: `http://localhost/disease-tracker/`
   - You should see the login/signup page

## Usage

### For Patients (Authenticated Users)

1. **Register Account**
   - Click "Sign Up" on the home page
   - Enter username, email, and password
   - Click "Create Account"

2. **Login**
   - Enter your username and password
   - Click "Log In"

3. **Submit Disease Report**
   - Select disease type from dropdown
   - Enter your location/address
   - Click "Submit Disease Report"
   - Report will be geocoded and added to the map

4. **View Reports**
   - See your submitted reports in the table
   - View all reports on the heat map

### For Guests (Unauthenticated Users)

1. **View Heat Map**
   - Click "Continue as Guest" on home page
   - Click "View NCR Disease Heat Map"
   - Use filters to view specific diseases or time ranges

2. **Filter Data**
   - Select disease type from dropdown
   - Choose time range (30 days, 90 days, etc.)
   - Click "Refresh Data" to reload

## Project Structure

```
disease-tracker/
├── index.html              # Main application page
├── config/
│   └── config.php         # Database and app configuration
├── includes/
│   ├── header.php         # Common header
│   ├── navbar.php         # Navigation bar
│   ├── footer.php         # Common footer
│   └── security.php       # Security functions
├── api/
│   ├── auth.php           # Authentication endpoints
│   ├── cases.php          # Case report CRUD endpoints
│   └── map-data.php       # Heat map data endpoint
├── classes/
│   ├── Database.php       # Database connection class
│   ├── User.php           # User model
│   └── CaseReport.php     # Case report model
├── js/
│   ├── auth.js            # Authentication manager
│   ├── reports.js         # Report manager
│   ├── map.js             # Map manager
│   └── notifications.js   # Notification system
├── database/
│   ├── schema.sql         # Database schema
│   └── init.php           # Database initialization script
└── README.md              # This file
```

## API Endpoints

### Authentication API (`api/auth.php`)

- `POST ?action=register` - Register new user
- `POST ?action=login` - Login user
- `POST ?action=logout` - Logout user
- `GET ?action=check` - Check session status

### Case Reports API (`api/cases.php`)

- `POST ?action=create` - Create case report (requires auth)
- `GET ?action=list` - List case reports
- `PUT ?action=update` - Update case report (requires auth)
- `DELETE ?action=delete` - Delete case report (requires auth)

### Map Data API (`api/map-data.php`)

- `GET ?disease_type=<type>&days=<days>` - Get heat map data (public)

## Security Features

- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- CSRF token validation
- Rate limiting for login attempts
- Secure session management
- HTTP security headers

## Troubleshooting

### Database Connection Error

- Verify XAMPP MySQL is running
- Check database credentials in `config/config.php`
- Ensure database `disease_tracker` exists

### Geocoding Not Working

- Check internet connection (Nominatim API requires internet)
- Try more specific addresses (e.g., "Quezon City, Metro Manila")
- Wait a few seconds between requests (API rate limiting)

### Session Expired

- Sessions expire after 30 minutes of inactivity
- Simply log in again to continue

### Map Not Loading

- Check browser console for JavaScript errors
- Ensure Leaflet.js CDN is accessible
- Verify map container is visible

## Development

### Adding New Disease Types

1. Open phpMyAdmin
2. Navigate to `disease_tracker` database
3. Open `disease_types` table
4. Insert new row with disease name, description, and color code

### Modifying Session Timeout

Edit `config/config.php`:
```php
ini_set('session.gc_maxlifetime', 1800); // 30 minutes (in seconds)
```

## License

This project is for educational purposes.

## Support

For issues or questions, please check the troubleshooting section or review the code comments.
