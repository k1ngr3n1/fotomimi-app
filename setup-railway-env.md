# Railway Environment Variables Setup

## Option 1: Using Railway Dashboard (Recommended)

1. Go to your Railway project dashboard
2. Navigate to the "Variables" tab
3. Add the following environment variables:

### Essential Variables (Required)
```
APP_NAME=Fotomimi
APP_ENV=production
APP_KEY=base64:YH8kPK28CCbyGx3E6EAhMXs15YHpCy5Wjmr6/siXU8s=
APP_DEBUG=false
APP_URL=https://your-railway-domain.railway.app
LOG_CHANNEL=stderr
CACHE_DRIVER=array
CACHE_STORE=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite
```

### Optional Variables (Set to empty if not needed)
```
DB_CACHE_CONNECTION=
DB_CACHE_LOCK_CONNECTION=
DB_CACHE_LOCK_TABLE=
MEMCACHED_PERSISTENT_ID=
MEMCACHED_USERNAME=
MEMCACHED_PASSWORD=
REDIS_URL=
REDIS_USERNAME=
REDIS_PASSWORD=
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_URL=
AWS_ENDPOINT=
DYNAMODB_ENDPOINT=
SESSION_CONNECTION=
SESSION_STORE=
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=true
DB_QUEUE_CONNECTION=
SQS_SUFFIX=
MAIL_SCHEME=
MAIL_URL=
MAIL_USERNAME=
MAIL_PASSWORD=
POSTMARK_MESSAGE_STREAM_ID=
MAIL_LOG_CHANNEL=
POSTMARK_TOKEN=
RESEND_KEY=
LOG_SLACK_WEBHOOK_URL=
PAPERTRAIL_URL=
PAPERTRAIL_PORT=
LOG_STDERR_FORMATTER=
SLACK_BOT_USER_OAUTH_TOKEN=
SLACK_BOT_USER_DEFAULT_CHANNEL=
```

## Option 2: Using railway.env file

1. Copy the contents of `railway.env` file
2. In your Railway project, go to "Variables" tab
3. Click "Add Variables from File"
4. Paste the contents and save

## Option 3: Using Railway CLI

1. Install Railway CLI: `npm i -g @railway/cli`
2. Login: `railway login`
3. Link your project: `railway link`
4. Set variables: `railway variables set APP_KEY=base64:YH8kPK28CCbyGx3E6EAhMXs15YHpCy5Wjmr6/siXU8s=`

## Important Notes

1. **Update APP_URL**: Replace `https://your-railway-domain.railway.app` with your actual Railway domain
2. **Database**: This configuration uses SQLite in `/tmp/` which is ephemeral. For persistent data, consider:
   - Adding a PostgreSQL plugin in Railway
   - Using an external database service
3. **File Storage**: Uses local storage which is ephemeral. For persistent file storage, consider:
   - AWS S3
   - Cloudinary
   - Railway's file storage

## After Setting Variables

1. Redeploy your application in Railway
2. The Laravel warnings should be resolved
3. Your application should start successfully
