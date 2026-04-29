# Start with the official PHP 8.2 Apache image
FROM php:8.2-apache

# 1. Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    iputils-ping \
    git \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# 2. Install Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Enable Apache mod_rewrite for MVC routing
RUN a2enmod rewrite

# 4. Set the working directory
WORKDIR /var/www/html

# 5. Copy Composer files FIRST (Optimization)
# The asterisk in composer.lock* makes it optional so the build won't fail if it's missing.
COPY composer.json composer.lock* ./

# 6. Install dependencies
# If composer.json is empty or doesn't exist, this step might fail.
# We use --no-scripts to prevent running custom scripts before the app code is copied.
RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

# 7. Change Apache DocumentRoot to the public/ folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 8. Copy the rest of your application code
COPY . .

# 9. Finalize the autoloader now that all classes are present
RUN composer dump-autoload --optimize

# 10. Give Apache the correct permissions
RUN chown -R www-data:www-data /var/www/html

# 11. Fix Git ownership issue
RUN git config --global --add safe.directory /var/www/html