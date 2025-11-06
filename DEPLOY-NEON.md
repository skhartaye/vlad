# Deploying Disease Tracker to Neon + Vercel/Railway

Neon provides PostgreSQL database hosting. You'll need a separate service for PHP hosting.

## Option 1: Neon (Database) + Railway (PHP Hosting)

### Step 1: Set Up Neon Database

1. **Create Neon Account**
   - Go to https://neon.tech/
   - Sign up for free account
   - Create a new project

2. **Get Connection Details**
   - Copy your connection string from Neon dashboard
   - Format: `postgresql://username:password@host/database?sslmode=require`
   - Note down: host, database name, username, password

3. **Run PostgreSQL Schema**
   - In Neon console, go to SQL Editor
   - Copy contents of `database/schema-postgresql.sql`
   - Paste and execute

### Step 2: Update Your Code

1. **Replace Database Class**
   ```bash
   # Backup original
   mv classes/Database.php classes/Database-MySQL.php
   
   # Use PostgreSQL version
   mv classes/Database-PostgreSQL.php classes/Database.php
   ```

2. **Update Config File**
   - Copy `config/config-neon.php` to `config/config.php`
   - Update with your Neon credentials:
   ```php
   define('DB_HOST', 'ep-xxx-xxx.neon.tech');
   define('DB_NAME', 'neondb');
   define('DB_USER', 'your-username');
   define('DB_PASS', 'your-password');
   define('DB_PORT', 5432);
   ```

3. **Update CaseReport.php Queries**
   - PostgreSQL uses `RETURNING id` instead of `LAST_INSERT_ID()`
   - See changes needed below

### Step 3: Deploy to Railway

1. **Create Railway Account**
   - Go to https://railway.app/
   - Sign up with GitHub

2. **Deploy from GitHub**
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Choose your `vlad` repository
   - Railway will auto-detect PHP

3. **Add Environment Variables**
   - In Railway dashboard, go to Variables
   - Add your Neon credentials:
   ```
   DB_HOST=ep-xxx-xxx.neon.tech
   DB_NAME=neondb
   DB_USER=your-username
   DB_PASS=your-password
   DB_PORT=5432
   ENVIRONMENT=production
   ```

4. **Configure PHP**
   - Create `railway.json` in root:
   ```json
   {
     "build": {
       "builder": "NIXPACKS"
     },
     "deploy": {
       "startCommand": "php -S 0.0.0.0:$PORT -t .",
       "restartPolicyType": "ON_FAILURE"
     }
   }
   ```

5. **Deploy**
   - Railway will automatically deploy
   - Get your public URL from dashboard

## Option 2: Use Traditional PHP Hosting

If Neon doesn't work well, use **MySQL-based hosting** instead:

### Recommended: Hostinger or Namecheap

1. **Sign up** for shared hosting (~$2-3/month)
2. **Upload files** via FTP or Git
3. **Create MySQL database** in cPanel
4. **Import** `database/schema.sql` via phpMyAdmin
5. **Update** `config/config.php` with database credentials
6. **Access** your domain

This is simpler and works out of the box with your current MySQL code.

## PostgreSQL Code Changes Needed

### In `classes/CaseReport.php`

Change this:
```php
if ($stmt->execute()) {
    $this->id = $this->conn->lastInsertId();
    return $this->id;
}
```

To this:
```php
if ($stmt->execute()) {
    $result = $stmt->fetch();
    $this->id = $result['id'];
    return $this->id;
}
```

And update the INSERT query to:
```php
$query = "INSERT INTO " . $this->table_name . " 
         (user_id, disease_type_id, address, latitude, longitude) 
         VALUES (:user_id, :disease_type_id, :address, :latitude, :longitude)
         RETURNING id";
```

### In `classes/User.php`

Same changes for the `create()` method.

## My Recommendation

**For easiest deployment:** Use **Hostinger** or **InfinityFree** with MySQL (no code changes needed)

**For learning cloud:** Use **Railway** with **Neon** (requires PostgreSQL changes)

**For free tier:** Use **InfinityFree** (completely free, MySQL included)

## Testing Locally with PostgreSQL

If you want to test PostgreSQL locally:

1. Install PostgreSQL: https://www.postgresql.org/download/
2. Create database: `createdb disease_tracker`
3. Run schema: `psql disease_tracker < database/schema-postgresql.sql`
4. Update config.php with local PostgreSQL credentials
5. Test the application

## Need Help?

Let me know which hosting option you choose and I can help with the specific setup!
