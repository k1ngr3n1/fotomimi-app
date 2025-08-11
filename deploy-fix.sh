#!/bin/bash

echo "=== Fixing AWS S3 Storage Dependencies ==="

# Install the required AWS S3 Flysystem adapter
echo "Installing league/flysystem-aws-s3-v3..."
composer require league/flysystem-aws-s3-v3:^3.0

# Clear Laravel caches
echo "Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Test storage configuration
echo "Testing storage configuration..."
php artisan storage:test

echo "=== Deployment fix completed ==="
echo ""
echo "If you're using S3 storage, make sure to set these environment variables:"
echo "MAIN_STORAGE_DRIVER=s3"
echo "AWS_ACCESS_KEY_ID=your_access_key"
echo "AWS_SECRET_ACCESS_KEY=your_secret_key"
echo "AWS_DEFAULT_REGION=your_region"
echo "AWS_BUCKET=your_bucket_name"
echo ""
echo "If you're using local storage, set:"
echo "MAIN_STORAGE_DRIVER=local"
