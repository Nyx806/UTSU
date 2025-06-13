#!/bin/sh
set -e

# Wait for database to be ready
echo "Waiting for database..."
while ! nc -z db 5432; do
    sleep 0.1
done
echo "Connection to db (172.18.0.2) 5432 port [tcp/postgresql] succeeded!"
echo "Database is up!"

# Create database if it doesn't exist
echo "Creating database if it doesn't exist..."
psql "postgresql://postgres:postgres@db:5432/postgres" -tc "SELECT 1 FROM pg_database WHERE datname = 'utsu'" | grep -q 1 || \
psql "postgresql://postgres:postgres@db:5432/postgres" -c "CREATE DATABASE utsu"

# Create migrations table if it doesn't exist
echo "Creating migrations table if it doesn't exist..."
php bin/console doctrine:migrations:sync-metadata-storage

# Run migrations
echo "Running migrations..."
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate --no-interaction

# Start PHP server
exec php -S 0.0.0.0:80 -t public/ 