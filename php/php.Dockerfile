FROM php:8.2-fpm

RUN apt-get -y update \
    && apt-get install -y libssl-dev pkg-config libzip-dev unzip git

RUN pecl install zip mongodb \
    && docker-php-ext-enable zip \
    && docker-php-ext-enable mongodb

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY . /app
COPY --chown=www:www . /app

USER www

EXPOSE 9000
CMD ["php-fpm"]
