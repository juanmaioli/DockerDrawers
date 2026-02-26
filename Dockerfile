# Dockerfile para PHP 8.3 con Apache y extensiones necesarias
FROM php:8.3-apache

# Instalamos dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli \
    && docker-php-ext-enable gd mysqli

# Habilitamos módulos de Apache if son necesarios (ej. mod_rewrite)
RUN a2enmod rewrite

# Ajustamos permisos básicos
RUN chown -R www-data:www-data /var/www/html
