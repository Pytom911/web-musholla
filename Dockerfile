FROM php:8.4-cli

# Install extension PHP yang dibutuhkan
RUN docker-php-ext-install mysqli mbstring

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files terlebih dahulu
COPY composer.json composer.lock ./

# Install dependency
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Copy seluruh project
COPY . .

# Railway menggunakan PORT environment variable
CMD sh -c 'php -S 0.0.0.0:${PORT:-8080} -t /app'