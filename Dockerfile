FROM php:8.3-cli

WORKDIR /app

RUN apt-get update && apt-get install -y unzip git libsqlite3-dev sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . .

RUN mkdir -p var/data && \
    [ ! -f var/data/db.sqlite ] && touch var/data/db.sqlite || true && \
    chmod 777 var/data/db.sqlite

EXPOSE 8000
