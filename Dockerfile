# Base image
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

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
    npm \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    pkg-config \
    nginx \
    supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath pdo_pgsql pgsql intl zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Install Node dependencies and build assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Copy Nginx configuration
COPY ./nginx.conf /etc/nginx/sites-available/default

# Copy Supervisor configuration
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Add Laravel worker for loan reminders
RUN echo "[program:loan_reminder]\ncommand=php /var/www/html/artisan loan:reminder\nautostart=true\nautorestart=true\nuser=www-data\nstdout_logfile=/var/www/html/storage/logs/loan_reminder.log\nstderr_logfile=/var/www/html/storage/logs/loan_reminder_err.log" \
    >> /etc/supervisor/conf.d/supervisord.conf

# Expose HTTP port
EXPOSE 80

# Start Supervisor (runs PHP-FPM, Nginx, and worker)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
