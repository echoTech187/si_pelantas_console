FROM php:8.2-apache

# Install dependencies and required PHP extensions for CodeIgniter 4
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install \
    intl \
    mysqli \
    pdo_mysql \
    zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Run composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configure Apache DocumentRoot to point to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set permissions for CodeIgniter writable storage
RUN mkdir -p /var/www/html/writable/cache /var/www/html/writable/logs /var/www/html/writable/session /var/www/html/writable/uploads \
    && chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

EXPOSE 80

CMD ["apache2-foreground"]
