# Base image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    nodejs \
    npm \
    libpq-dev \
    libicu-dev \
    pkg-config \
    libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath pdo_pgsql pgsql intl zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
RUN npm install && npm run build

# Copy Nginx configuration
COPY ./nginx.conf /etc/nginx/sites-available/default

# Copy Supervisor configuration
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Expose ports
EXPOSE 80 8080

# Add worker command to supervisord.conf if not already added
RUN echo "[program:loan_reminder]\n\
command=php /var/www/html/artisan loan:reminder\n\
autostart=true\n\
autorestart=true\n\
user=www-data\n\
stdout_logfile=/var/www/html/storage/logs/loan_reminder.log\n\
stderr_logfile=/var/www/html/storage/logs/loan_reminder_err.log" \
>> /etc/supervisor/conf.d/supervisord.conf

# Start Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
