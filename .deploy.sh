#!/bin/bash

ENV=$1  # Môi trường: dev, staging, hoặc prod
REPO_PATH="/var/www/kodingsoft/company_api"

echo "Deploying to $ENV environment..."

# Di chuyển đến thư mục dự án
cd $REPO_PATH || { echo "Failed to change to $REPO_PATH"; exit 1; }

echo "🧹 Cleaning working directory..."
git reset --hard HEAD
git clean -fd
# Pull code mới nhất từ Git
git pull --rebase origin develop || { echo "Git pull failed"; exit 1; }

# Cài đặt composer dependencies
composer install --no-dev --optimize-autoloader || { echo "Composer install failed"; exit 1; }

# Chạy migration (nếu cần)
if [ "$ENV" != "prod" ]; then
    php artisan migrate --force
fi
# Seeder (chạy ở mọi môi trường – hoặc thêm điều kiện nếu muốn chỉ chạy ở dev)
php artisan db:seed --class=RoleSeeder || { echo "RoleSeeder failed"; exit 1; }
php artisan db:seed --class=UserSeeder || { echo "UserSeeder failed"; exit 1; }
# Xóa cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Build lại Docker image
docker-compose -f /var/www/kodingsoft/docker-compose.$ENV.yml build laravel
docker-compose -f /var/www/kodingsoft/docker-compose.$ENV.yml up -d laravel

echo "Deployment to $ENV completed successfully!"