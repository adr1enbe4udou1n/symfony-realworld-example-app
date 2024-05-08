FROM gitea.okami101.io/okami101/frankenphp:8.3

ARG USER=www-data

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

RUN \
    useradd -D ${USER}; \
    setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
    chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy; \
    mkdir var && chown ${USER}:${USER} var;

USER ${USER}
