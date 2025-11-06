# Deploying to Vercel with Neon Database

## Prerequisites
- Vercel account (https://vercel.com)
- Neon database account (https://neon.tech)
- GitHub repository

## Step 1: Set Up Neon Database

1. **Create Neon Project**
   - Go to https://console.neon.tech/
   - Create a new project
   - Note your connection details

2. **Run PostgreSQL Schema**
   - In Neon SQL Editor, run the contents of `database/schema-postgresql.sql`
   - This creates all tables and initial data

3. **Get Connection Details**
   - Host: `ep-xxxxx.neon.tech`
   - Database: `neondb`
   - User: Your username
   - Password: Your password
   - Port: `5432`

## Step 2: Update Code for PostgreSQL

1. **Replace Database Class**
   ```bash
   # Backup MySQL version
   cp classes/Database.php classes/Database-MySQL-backup.php
   
   # Use PostgreSQL version
   cp classes/Database-PostgreSQL.php classes/Database.php
   ```

2. **Update CaseReport.php and User.php**
   - Change `lastInsertId()` to use `RETURNING id`
   - See PostgreSQL changes in DEPLOY-NEON.md

## Step 3: Deploy to Vercel

1. **Push to GitHub**
   ```bash
   git add .
   git commit -m "Add Vercel configuration"
   git push
   ```

2. **Import to Vercel**
   - Go to https://vercel.com/new
   - Click "Import Project"
   - Select your GitHub repository
   - Click "Import"

3. **Add Environment Variables**
   In Vercel dashboard, go to Settings → Environment Variables and add:
   
   ```
   DB_HOST = ep-xxxxx.neon.tech
   DB_NAME = neondb
   DB_USER = your-username
   DB_PASS = your-password
   DB_PORT = 5432
   ENVIRONMENT = production
   BASE_URL = https://your-project.vercel.app/
   ```

4. **Deploy**
   - Click "Deploy"
   - Wait for deployment to complete
   - Visit your URL: `https://your-project.vercel.app`

## Step 4: Test Your Deployment

1. Open your Vercel URL
2. Try to sign up for an account
3. Log in
4. Submit a disease report
5. View the heat map

## Troubleshooting

### Issue: "Database connection failed"
- Check your Neon credentials in Vercel environment variables
- Make sure Neon database is running
- Verify SSL is enabled (Neon requires SSL)

### Issue: "API not found" or 404 errors
- Make sure `vercel.json` is in the root directory
- Redeploy the project
- Check Vercel function logs

### Issue: Session errors
- Vercel serverless functions are stateless
- Sessions might not work as expected
- Consider using JWT tokens instead

## Alternative: Use MySQL Hosting

If PostgreSQL conversion is too complex, use traditional PHP hosting with MySQL:

**Recommended Hosts:**
- **Hostinger** (~$2/month) - https://hostinger.com
- **InfinityFree** (Free) - https://infinityfree.net
- **000webhost** (Free) - https://000webhost.com

These work with your current MySQL code without any changes!

## Files Added for Vercel

- `vercel.json` - Vercel configuration
- `api/index.php` - API entry point
- Updated `config/config.php` - Environment variable support

## Important Notes

⚠️ **Vercel Limitations:**
- Serverless functions have 10-second timeout
- Sessions might not persist (use JWT instead)
- File uploads are limited
- Better for APIs than full PHP apps

💡 **Better Alternative:**
Consider using **Railway** (https://railway.app) which has better PHP support and works great with Neon.

## Need Help?

If you encounter issues, check:
1. Vercel deployment logs
2. Neon database connection
3. Environment variables are set correctly
4. PHP version compatibility (Vercel uses PHP 8.x)
