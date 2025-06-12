FROM dunglas/frankenphp

ENV SERVER_NAME=your-domain.exemple.com
ENV APP_RUNTIME=Runtime\\FrankenPhpSymfony\\Runtime
ENV APP_ENV=prod
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

WORKDIR /app

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    libzip-dev \
    && docker-php-ext-install zip

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier uniquement composer.json et composer.lock d'abord
COPY composer.json composer.lock ./

# Copier ensuite tout le reste du code (y compris bin/console)
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

