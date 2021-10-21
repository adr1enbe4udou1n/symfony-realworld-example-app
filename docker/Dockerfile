FROM php:8.0-fpm-alpine3.13

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions @composer opcache pdo_pgsql
