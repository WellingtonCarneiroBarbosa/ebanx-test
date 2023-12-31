# !/bin/bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    bash -c "composer install --ignore-platform-reqs \
    && touch database/database.sqlite \
    && cp .env.example .env \
    && php artisan key:generate"
