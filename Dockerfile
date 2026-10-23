FROM php:8.4-cli

# Install dependency untuk PHP extensions
RUN apt-get update \
    && apt-get install -y libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli mbstring

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy Composer files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Copy project
COPY . .

# Start PHP server
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /app"]