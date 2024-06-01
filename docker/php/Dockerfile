FROM php:8.3-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mbstring pdo pdo_mysql zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копирование приложения
COPY ./src /var/www/html

RUN pecl install redis \
    && docker-php-ext-enable redis

# Установка зависимостей Composer
RUN composer install --ignore-platform-reqs

# Настройка прав доступа
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]