FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones PHP para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql zip

# Habilitar mod_rewrite de Apache para las rutas de Laravel
RUN a2enmod rewrite

# Cambiar la raíz del servidor a la carpeta public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Obtener Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar el proyecto
WORKDIR /var/www/html
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Configurar permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

# Al iniciar el contenedor, limpiar cachés, ejecutar migraciones y luego iniciar Apache
CMD php artisan optimize:clear && php artisan migrate --force && apache2-foreground
