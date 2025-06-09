FROM php:8.1-fpm

RUN apt update && \
    apt install -y curl git

WORKDIR /app

ENTRYPOINT [ "php-fpm" ]
