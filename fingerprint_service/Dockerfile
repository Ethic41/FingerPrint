FROM dahirmuhammaddahir/secureng-php-apache

WORKDIR /var/www/html

COPY . .

RUN composer update && \
    composer install && \
    cd thirdparty/grpc-fingerprint-engine/src/php/ && \
    composer update && \
    composer install && \
    apt update && apt install -y net-tools

