# PHP
FROM php:8.2-cli

# System dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Application directory
WORKDIR /var/www

# Copy application
COPY . .

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel permissions
RUN chmod -R 775 storage bootstrap/cache

# Render will provide $PORT
EXPOSE 10000

# Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=${PORT}