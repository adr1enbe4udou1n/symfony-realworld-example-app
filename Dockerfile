FROM gitea.okami101.io/okami101/frankenphp as base

WORKDIR /app

COPY bin bin/
COPY config config/
COPY fixtures fixtures/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY templates templates/
COPY vendor vendor/
COPY .env.prod .env
COPY composer.json composer.lock ./

RUN mkdir var && chown www-data:www-data var
