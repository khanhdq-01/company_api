#!/bin/bash

ENV=$1
REPO_PATH="/var/www/kodingsoft/company_api"
DOCKER_CONTAINER=laravel_prod

echo "Deploying to $ENV environment..."

cd $REPO_PATH || { echo "Failed to change to $REPO_PATH"; exit 1; }

echo "🧹 Cleaning working directory..."
git reset --hard HEAD
git clean -fd
git pull --rebase origin develop || { echo "Git pull failed"; exit 1; }

# Composer install trong container
docker exec -i $DOCKER_CONTAINER composer install --no-dev --optimize-autoloader || { echo "Composer install failed"; exit 1; }

# Migration (chỉ khi không phải prod)
if [ "$ENV" != "prod" ]; then
    docker exec -i $DOCKER_CONTAINER php artisan migrate --force
fi

# Seeder
docker exec -i $DOCKER_CONTAINER php artisan db:seed --class=RoleSeeder || { echo "RoleSeeder failed"; exit 1; }
docker exec -i $DOCKER_CONTAINER php artisan db:seed --class=UserSeeder || { echo "UserSeeder failed"; exit 1; }

# Cache clear
docker exec -i $DOCKER_CONTAINER php artisan cache:clear
docker exec -i $DOCKER_CONTAINER php artisan config:cache
docker exec -i $DOCKER_CONTAINER php artisan route:cache

# Rebuild container nếu muốn
docker-compose -f /var/www/kodingsoft/docker-compose.$ENV.yml build laravel
docker-compose -f /var/www/kodingsoft/docker-compose.$ENV.yml up -d laravel

echo "✅ Deployment to $ENV completed successfully!"
