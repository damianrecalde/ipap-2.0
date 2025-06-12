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
    nodejs \
    npm \
    openssl

# Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql intl zip

# Instalar Composer desde su imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear y usar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto (lo hará en tiempo de build, importante para CI/CD)
COPY . .

# Copiar el entrypoint y darle permisos
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Asignar permisos al código (solo si necesario)
RUN chown -R www-data:www-data /var/www/html

# Cambiar a usuario seguro
USER www-data

# Usar el entrypoint para ejecutar Composer, NPM y Encore
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Comando por defecto si no se sobreescribe (desde entrypoint, no se usa aquí)
CMD ["php-fpm"]
