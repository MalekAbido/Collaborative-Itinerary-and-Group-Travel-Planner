# Start with the official PHP 8.2 Apache image
FROM php:8.2-apache

# Install the PDO and MySQL extensions required for TaskFlow
RUN apt-get update && apt-get install -y \
    iputils-ping \
    && docker-php-ext-install pdo pdo_mysql
# Enable Apache mod_rewrite for MVC routing (.htaccess)
RUN a2enmod rewrite

# Change Apache DocumentRoot to the public/ folder (Security Best Practice)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Give Apache the correct permissions
RUN chown -R www-data:www-data /var/www/html