FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Set Xdebug configuration (coverage mode)
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# Set working directory and permissions
WORKDIR /var/www

# Install PHP dependencies
COPY . .
RUN composer install --optimize-autoloader --no-dev
