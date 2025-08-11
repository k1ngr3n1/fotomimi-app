# Use official PHP 8.3 image
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build assets
RUN npm ci --only=production --no-audit
RUN npm run build

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Create storage link
RUN php artisan storage:link

# Expose port 8000
EXPOSE 8000

# Start the application
CMD php artisan serve --host=0.0.0.0 --port=8000
