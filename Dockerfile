# Базовый образ PHP 8.2
FROM php:8.2-cli

# Устанавливаем системные зависимости и Node.js
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    curl \
    && docker-php-ext-install zip pdo pdo_mysql \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Устанавливаем Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www

# Копируем весь проект
COPY . .

# Устанавливаем зависимости Laravel
RUN composer install --no-dev --optimize-autoloader

# Устанавливаем зависимости Node и билдим Vite
RUN npm install && npm run build

# Генерируем ключ приложения (если ещё не сгенерирован)
RUN php artisan key:generate || true

# Чистим кэш конфигурации и маршрутов
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# Настройка сессий и базы для тестового проекта
ENV SESSION_DRIVER=file
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=:memory:

# Создаём необходимые папки
RUN mkdir -p storage/framework/sessions storage/framework/cache storage/framework/views

# Открываем порт для встроенного сервера
EXPOSE 10000

# Запуск встроенного PHP-сервера
CMD php -S 0.0.0.0:10000 -t public
