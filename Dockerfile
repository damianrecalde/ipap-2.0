FROM php:8.2-fpm-alpine

# Actualizar paquetes
RUN apk update

# Instalar dependencias necesarias
RUN apk add --no-cache \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    zlib-dev \
    bash \
    git \
    postgresql-dev \
    openssl  # Para certificados SSL si los necesitas

# Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar dependencias de compilación y luego eliminarlas
RUN apk add --no-cache --virtual .build-deps \
    autoconf dpkg-dev dpkg file g++ gcc libc-dev make && \
    apk del .build-deps

# Instalar Composer desde su imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Asignar permisos
RUN chown -R www-data:www-data /var/www/html

# Cambiar a usuario seguro
USER www-data

# Ejecutar PHP-FPM como proceso principal
CMD ["php-fpm"]
