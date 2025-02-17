FROM php:8.3-cli

WORKDIR /app

RUN apt-get update && apt-get install -y unzip git libsqlite3-dev sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . .

EXPOSE 8000
