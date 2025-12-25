FROM gitea.okami101.io/okami101/frankenphp:8.5

ENV APP_ENV=prod
ARG USER=www-data

WORKDIR /app

COPY bin bin/
COPY config config/
COPY fixtures fixtures/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY templates templates/
COPY .env.prod ./
COPY composer.json composer.lock ./

RUN \
    composer install --no-dev --optimize-autoloader; \
    useradd -D ${USER}; \
    setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
    chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy; \
    mkdir var && chown ${USER}:${USER} var;

USER ${USER}

ENV SERVER_NAME=:80
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
