#!/bin/bash

ENV=$1
REPO_PATH="/var/www/kodingsoft/company_api"
DOCKER_CONTAINER=laravel_prod
COMPOSE_FILE="/var/www/kodingsoft/docker-compose.$ENV.yml"

echo "📦 Deploying to $ENV environment..."

# 1. Chuyển vào thư mục code
cd $REPO_PATH || { echo "❌ Failed to change to $REPO_PATH"; exit 1; }

# 2. Reset và pull code mới nhất
echo "🧹 Cleaning working directory..."
git reset --hard HEAD
git clean -fd
git pull --rebase origin develop || { echo "❌ Git pull failed"; exit 1; }

# 3. Cài composer bên trong container Laravel
echo "📦 Installing composer dependencies in container..."
docker exec -i $DOCKER_CONTAINER composer install --no-dev --optimize-autoloader || { echo "❌ Composer install failed"; exit 1; }

# 4. Chạy migration (chỉ khi không phải prod)
if [ "$ENV" != "prod" ]; then
    echo "🛠 Running migrations..."
    docker exec -i $DOCKER_CONTAINER php artisan migrate --force || { echo "❌ Migration failed"; exit 1; }
fi

# 5. Seeder
echo "🌱 Running seeders..."
docker exec -i $DOCKER_CONTAINER php artisan db:seed --class=RoleSeeder --force || { echo "❌ RoleSeeder failed"; exit 1; }
docker exec -i $DOCKER_CONTAINER php artisan db:seed --class=UserSeeder --force || { echo "❌ UserSeeder failed"; exit 1; }

# 6. Clear & cache config
echo "🧹 Clearing & caching Laravel config..."
docker exec -i $DOCKER_CONTAINER php artisan cache:clear
docker exec -i $DOCKER_CONTAINER php artisan config:cache
docker exec -i $DOCKER_CONTAINER php artisan route:cache
# php artisan storage:link
docker exec -i $DOCKER_CONTAINER php artisan storage:link || { echo "❌ Storage link failed"; exit 1; }

# 7. Rebuild container Laravel
echo "🐳 Rebuilding Laravel container..."
docker-compose -f $COMPOSE_FILE build laravel
docker-compose -f $COMPOSE_FILE up -d laravel

echo "✅ Deployment to $ENV completed successfully!"
