FROM php:8.2-apache

# Activar mod_rewrite para Laravel
RUN a2enmod rewrite

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip

# Extensiones PHP necesarias para Laravel
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar el proyecto
COPY . .

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
