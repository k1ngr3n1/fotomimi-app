# Railway Deployment Guide

## Prerequisites
- Railway account
- PostgreSQL database (recommended for production)
- SMTP service for emails

## Environment Variables

Set these environment variables in your Railway project:

### Required Variables
```
APP_NAME=Fotomimi
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app
```

### Database (PostgreSQL recommended)
```
DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}
```

### Session and Cache
```
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Mail Configuration (example with Gmail)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=Fotomimi
```

## Deployment Steps

1. **Connect your repository to Railway**
2. **Add PostgreSQL service** to your project
3. **Set environment variables** as listed above
4. **Deploy** - Railway will automatically use the configuration files

## Post-Deployment

After deployment, run these commands in Railway's console:

```bash
# Run migrations
php artisan migrate

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link
```

## Troubleshooting

### Common Issues:

1. **"Application failed to respond"**
   - Check if APP_KEY is set
   - Verify database connection
   - Check logs in Railway dashboard

2. **Database connection errors**
   - Ensure PostgreSQL service is added
   - Verify environment variables are correct
   - Check if migrations have been run

3. **Asset loading issues**
   - Ensure `npm run build` completed successfully
   - Check if VITE_APP_NAME is set correctly

### Health Check
The application includes a health check endpoint at `/up` that Railway uses to verify the application is running.

### Nixpacks Issues
If you encounter Nixpacks errors like "nix-env -if .nixpacks/nixpkgs-*.nix did not complete successfully":
1. The project is now configured to use Dockerfile by default (more reliable)
2. If you need to use Nixpacks, ensure you have the latest version
3. Try clearing Railway's build cache
4. Check that all environment variables are properly set

## File Structure
- `railway.json` - Railway deployment configuration (uses Dockerfile)
- `Dockerfile` - Docker configuration for reliable builds
- `nixpacks.toml` - Alternative build configuration (if needed)
- `Procfile` - Alternative deployment method
- `deploy.sh` - Deployment script for post-deployment tasks
- `public/index.php` - Application entry point

## Build Configuration

This project uses **Dockerfile** for deployment, which is more reliable than Nixpacks. If you encounter issues with Docker, you can switch to Nixpacks by changing the `builder` in `railway.json` from `"DOCKERFILE"` to `"NIXPACKS"`.
