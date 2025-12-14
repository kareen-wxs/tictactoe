# Базовый PHP 8.2 CLI
FROM php:8.2-cli

# Устанавливаем зависимости для Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# Устанавливаем Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www

# Копируем весь проект
COPY . .

# Устанавливаем зависимости Laravel
RUN composer install --no-dev --optimize-autoloader

# Генерируем ключ приложения
RUN php artisan key:generate || true

# Чистим кэш конфигурации
RUN php artisan config:clear

# Настройка сессий и базы для тестового проекта
# (Файл базы не нужен, сессии хранятся в файлах)
ENV SESSION_DRIVER=file
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=:memory:

# Создаём пустую папку storage/framework/sessions
RUN mkdir -p storage/framework/sessions

# Открываем порт для Render
EXPOSE 10000

# Запуск встроенного PHP-сервера
CMD php -S 0.0.0.0:10000 -t public
