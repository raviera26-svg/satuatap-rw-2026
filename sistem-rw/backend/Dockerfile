FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql && a2enmod rewrite

# Pastikan custom vhost terpasang di Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

RUN chown -R www-data:www-data /var/www/html && \
    chown -R www-data:www-data /var/www/backend 2>/dev/null || true

