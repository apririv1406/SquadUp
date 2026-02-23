FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar DocumentRoot a /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf

# Copiar proyecto
COPY . /var/www/html

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]

