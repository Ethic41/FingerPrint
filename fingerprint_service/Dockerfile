FROM dahirmuhammaddahir/secureng-php-apache

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader && \
    cd thirdparty/grpc-fingerprint-engine/src/php/ && \
    composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader && \
    apt-get update && apt-get install -y --no-install-recommends net-tools && \
    rm -rf /var/lib/apt/lists/*

