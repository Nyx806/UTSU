# Build stage for Node.js assets
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY assets/ ./assets/
COPY webpack.config.js postcss.config.mjs ./
RUN npm run build

# Build stage for PHP dependencies
FROM composer:2 AS composer
WORKDIR /app
COPY composer.* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --optimize

# Final stage with FrankenPHP
FROM dunglas/frankenphp:1-alpine AS final
WORKDIR /app

# Install system dependencies
RUN apk add --no-cache \
    icu-libs \
    libzip \
    libpng \
    libjpeg-turbo \
    netcat-openbsd \
    postgresql \
    postgresql-client \
    postgresql-dev \
    php82-pdo \
    php82-pdo_pgsql \
    php82-pgsql

# Enable PostgreSQL extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Copy PHP extensions and vendor files
COPY --from=composer /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=composer /app/vendor/ ./vendor/

# Copy built assets
COPY --from=node_builder /app/public/build/ ./public/build/

# Copy application files
COPY . .

# Create necessary directories and set permissions
RUN mkdir -p var/cache var/log var/sessions && \
    chown -R www-data:www-data var

# Create .env.local with production settings
RUN echo "APP_ENV=prod" > .env.local && \
    echo "APP_SECRET=$(openssl rand -hex 16)" >> .env.local && \
    echo "MESSENGER_TRANSPORT_DSN=doctrine://default" >> .env.local

# Configure Caddy with HTTPS
RUN printf 'https://utsu.fr {\n\
    root * /app/public\n\
    encode gzip\n\
    php_server\n\
    file_server\n\
    try_files {path} {path}/ /index.php?{query}\n\
}\n' > /etc/caddy/Caddyfile

# Ensure .env exists
RUN [ -f .env ] || touch .env

# Copy and set up entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose ports
EXPOSE 80 443

# Set entrypoint and command
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]