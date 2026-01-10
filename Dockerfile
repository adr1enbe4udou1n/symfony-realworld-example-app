FROM gitea.okami101.io/okami101/frankenphp:8.5

ARG USER=www-data

RUN \
    useradd ${USER}; \
    setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
    chown -R ${USER}:${USER} /config/caddy /data/caddy; \
    mkdir -p /app; \
    chown -R ${USER}:${USER} /app

USER ${USER}

ENV APP_ENV=prod
ENV SERVER_NAME=:80
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

WORKDIR /app

COPY bin bin/
COPY config config/
COPY fixtures fixtures/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY templates templates/
COPY .env.prod .env
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader
