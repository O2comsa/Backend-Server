# Use an official PHP image
FROM php:8.1-fpm

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip

# Install additional PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

ENV AWS_SUPPRESS_PHP_DEPRECATION_WARNING=true

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Expose port 80
EXPOSE 80