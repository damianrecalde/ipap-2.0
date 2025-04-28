# Usar imagen base PHP con FPM en Alpine
FROM php:8.2-fpm-alpine

# Actualizar el índice de paquetes
RUN apk update

# Instalar dependencias necesarias para PHP y Composer
RUN apk add --no-cache \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    zlib-dev \
    bash \
    git \
    postgresql-dev \
    nginx \
    openssl  # Para generar certificados SSL

# Instalar las extensiones PDO y PDO para PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar dependencias de compilación
RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    dpkg-dev \
    dpkg \
    file \
    g++ \
    gcc \
    libc-dev \
    make

# Limpiar los paquetes de compilación después de la instalación
RUN apk del .build-deps

# Instalar Composer de la imagen oficial de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos del proyecto al contenedor
COPY . .

# Instalar dependencias PHP con Composer
#RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Establecer permisos de los archivos copiados para que www-data pueda acceder
RUN chown -R www-data:www-data /var/www/html

# Cambiar al usuario www-data para una mayor seguridad
USER www-data

# Exponer el puerto que el servidor PHP usará
EXPOSE 8000

# Comando por defecto para ejecutar el servidor PHP integrado
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
