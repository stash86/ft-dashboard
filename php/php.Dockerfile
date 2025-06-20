FROM php:8.4-fpm

RUN apt-get -y update \
    && apt-get install -y libssl-dev pkg-config libzip-dev unzip git

RUN pecl install zip \
    && docker-php-ext-enable zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ARG UID=1000
ARG GID=1000

RUN groupadd -g ${GID} www
RUN useradd -u ${UID} -ms /bin/bash -g www www

RUN docker-php-ext-install pdo_pgsql pgsql

USER www

EXPOSE 9000
CMD ["php-fpm"]
