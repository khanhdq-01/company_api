FROM php:8.2-fpm

# 1. Cài dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# 2. Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Làm việc tại thư mục dự án
WORKDIR /var/www

# 4. Copy source code vào container
COPY . .

# 5. Cài đặt Laravel dependencies
RUN composer install

# 6. Copy sẵn file env nếu có
COPY .env.example .env

# 7. Generate key (hoặc có thể chạy tay nếu lỗi)
RUN php artisan key:generate

# 8. Cổng mở 8000, dùng serve
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]