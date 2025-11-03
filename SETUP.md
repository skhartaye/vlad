# Disease Tracker - Setup Guide

## Quick Start (5 Minutes)

### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Run the installer
3. Install to default location (C:\xampp on Windows)
4. Complete installation

### Step 2: Start Services

1. Open XAMPP Control Panel
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Both should show green "Running" status

### Step 3: Copy Project Files

1. Copy the entire `disease-tracker` folder
2. Paste into: `C:\xampp\htdocs\`
3. Final path should be: `C:\xampp\htdocs\disease-tracker\`

### Step 4: Initialize Database

**Option A: Automatic (Recommended)**

1. Open your web browser
2. Go to: `http://localhost/disease-tracker/database/init.php`
3. Wait for "Setup complete!" message
4. Click "Go to Application"

**Option B: Manual via phpMyAdmin**

1. Open: `http://localhost/phpmyadmin`
2. Click "New" to create database
3. Database name: `disease_tracker`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Click "Import" tab
7. Choose file: `database/schema.sql`
8. Click "Go"

### Step 5: Access Application

1. Open browser
2. Go to: `http://localhost/disease-tracker/`
3. You should see the login page

## Configuration

### Database Settings

Edit `config/config.php` if you need to change database credentials:

```php
define('DB_HOST', 'localhost');      // Database host
define('DB_NAME', 'disease_tracker'); // Database name
define('DB_USER', 'root');           // Database username
define('DB_PASS', '');               // Database password (empty by default)
```

### PHP Settings

Ensure these PHP extensions are enabled in `php.ini`:

- `extension=pdo_mysql`
- `extension=curl`
- `extension=mbstring`

To check/enable:
1. Open XAMPP Control Panel
2. Click "Config" next to Apache
3. Select "PHP (php.ini)"
4. Search for extensions above
5. Remove `;` at start of line to enable
6. Save and restart Apache

### Apache Virtual Host (Optional)

For cleaner URLs like `http://disease-tracker.local/`:

1. Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Add:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/disease-tracker"
    ServerName disease-tracker.local
    <Directory "C:/xampp/htdocs/disease-tracker">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Edit `C:\Windows\System32\drivers\etc\hosts` (as Administrator)
4. Add: `127.0.0.1 disease-tracker.local`
5. Restart Apache
6. Access: `http://disease-tracker.local/`

## Testing the Installation

### 1. Test Database Connection

Create `test-db.php` in project root:

```php
<?php
require_once 'config/config.php';
require_once 'classes/Database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✓ Database connection successful!";
} else {
    echo "✗ Database connection failed!";
}
?>
```

Access: `http://localhost/disease-tracker/test-db.php`

### 2. Test User Registration

1. Go to application home page
2. Click "Sign Up"
3. Fill in:
   - Username: `testuser`
   - Email: `test@example.com`
   - Password: `password123`
4. Click "Create Account"
5. Should see success message

### 3. Test Login

1. Enter username: `testuser`
2. Enter password: `password123`
3. Click "Log In"
4. Should see main dashboard

### 4. Test Case Report

1. After logging in
2. Select disease: "Dengue"
3. Enter location: "Quezon City, Metro Manila"
4. Click "Submit Disease Report"
5. Should see success notification
6. Report appears in table

### 5. Test Heat Map

1. Click "View NCR Disease Heat Map"
2. Map should load with markers
3. Try filtering by disease type
4. Try changing time range
5. Click "Refresh Data"

## Common Issues

### Issue: "Database connection failed"

**Solution:**
- Verify MySQL is running in XAMPP
- Check database name is `disease_tracker`
- Verify credentials in `config/config.php`

### Issue: "Could not geocode address"

**Solution:**
- Check internet connection
- Use more specific addresses
- Try: "City Name, Province, Country"
- Example: "Manila, Metro Manila, Philippines"

### Issue: "Session expired" immediately

**Solution:**
- Check PHP session settings
- Ensure `session.save_path` is writable
- In XAMPP: `C:\xampp\tmp` should exist

### Issue: Blank page or errors

**Solution:**
- Enable error display in `config/config.php`:
  ```php
  define('ENVIRONMENT', 'development');
  ```
- Check Apache error log: `C:\xampp\apache\logs\error.log`
- Check PHP error log: `C:\xampp\php\logs\php_error_log`

### Issue: Map not loading

**Solution:**
- Check browser console (F12)
- Verify internet connection (Leaflet CDN)
- Clear browser cache
- Try different browser

### Issue: "Access denied" for database

**Solution:**
- Default XAMPP MySQL has no password
- If you set a password, update `config/config.php`
- Reset MySQL password in phpMyAdmin if needed

## Security Checklist for Production

⚠️ **Before deploying to production:**

- [ ] Change database password
- [ ] Set `ENVIRONMENT` to `'production'` in config
- [ ] Enable HTTPS and set `session.cookie_secure` to `1`
- [ ] Update `BASE_URL` in config
- [ ] Disable directory listing
- [ ] Remove `test-db.php` and other test files
- [ ] Set proper file permissions
- [ ] Enable PHP error logging (not display)
- [ ] Configure firewall rules
- [ ] Regular database backups

## Performance Optimization

### Enable PHP OPcache

Edit `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

### MySQL Optimization

In phpMyAdmin, run:
```sql
OPTIMIZE TABLE users;
OPTIMIZE TABLE case_reports;
OPTIMIZE TABLE disease_types;
```

### Apache Optimization

Enable compression in `httpd.conf`:
```apache
LoadModule deflate_module modules/mod_deflate.so
```

## Backup and Restore

### Backup Database

**Via phpMyAdmin:**
1. Open phpMyAdmin
2. Select `disease_tracker` database
3. Click "Export"
4. Choose "Quick" method
5. Click "Go"
6. Save SQL file

**Via Command Line:**
```bash
cd C:\xampp\mysql\bin
mysqldump -u root disease_tracker > backup.sql
```

### Restore Database

**Via phpMyAdmin:**
1. Open phpMyAdmin
2. Select `disease_tracker` database
3. Click "Import"
4. Choose backup SQL file
5. Click "Go"

**Via Command Line:**
```bash
cd C:\xampp\mysql\bin
mysql -u root disease_tracker < backup.sql
```

## Getting Help

1. Check error logs
2. Review README.md
3. Check browser console (F12)
4. Verify all setup steps completed
5. Test with simple examples first

## Next Steps

After successful setup:

1. Create test user account
2. Submit sample disease reports
3. Explore heat map features
4. Test filtering and refresh
5. Try guest access mode
6. Review security settings
7. Plan production deployment

---

**Setup Complete!** 🎉

Your Disease Tracker application is now ready to use.
