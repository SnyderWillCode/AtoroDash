FROM php:8.3-fpm

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    zip \
    unzip \
    git \
    cron \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        gd \
        pdo \
        pdo_mysql \
        mysqli \
        bcmath \
        xml \
        mbstring \
        curl \
        zip \
		redis

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader

# Copy rest of the application
COPY . .

# Add a cron job
RUN (crontab -l ; echo "* * * * * cd /var/www/html && php mythicaldash schedule_work >> /dev/null 2>&1") | crontab
RUN (crontab -l ; echo "* * * * * cd /var/www/html/storage/cron && php runner.php >> /dev/null 2>&1") | crontab
RUN (crontab -l ; echo "* * * * * cd /var/www/html/storage/cron && bash runner.php >> /dev/null 2>&1") | crontab

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html