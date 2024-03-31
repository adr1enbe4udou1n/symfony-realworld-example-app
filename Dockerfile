ARG base_image
FROM ${base_image} as base

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

RUN composer dump-env prod

RUN mkdir var && chown www-data:www-data var
