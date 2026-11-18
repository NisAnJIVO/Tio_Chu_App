FROM php:8.2-cli-alpine

# Instalar dependencias del sistema y librerías para extensiones PHP
RUN apk add --no-cache \
    curl \
    git \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    nodejs \
    npm

# Instalar extensiones de PHP requeridas por Laravel y PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar todo el código al contenedor
COPY . .

# Instalar dependencias de Composer y Node
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install
RUN npm run build

# Permisos de carpetas para storage y caché
RUN chmod -R 777 /app/storage /app/bootstrap/cache

# Puerto que expone Render por defecto
ENV PORT=10000
EXPOSE 10000

# Script de arranque
CMD php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT}
