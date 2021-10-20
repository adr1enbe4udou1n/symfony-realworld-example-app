FROM php:8.0-apache

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions @composer opcache pdo_pgsql

COPY bin bin/
COPY config config/
COPY fixtures fixtures/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY templates templates/
COPY translations translations/
COPY vendor vendor/
COPY composer.json composer.lock ./

RUN chown -R www-data:www-data var

EXPOSE 80

ENTRYPOINT ["start-container"]
