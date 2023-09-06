FROM php:8.2-fpm

RUN apt-get -y update \
    && apt-get install -y libssl-dev pkg-config libzip-dev unzip git

RUN pecl install zip mongodb \
    && docker-php-ext-enable zip \
    && docker-php-ext-enable mongodb

EXPOSE 9000
CMD ["php-fpm"]
