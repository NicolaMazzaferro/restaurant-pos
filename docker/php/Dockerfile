FROM php:8.4-fpm-alpine

# Build deps + librerie *-dev per compilare le estensioni
RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS autoconf make gcc g++ pkgconfig \
        freetype-dev libjpeg-turbo-dev libpng-dev libwebp-dev \
        icu-dev oniguruma-dev libzip-dev; \
    \
    # Compila estensioni PHP
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql bcmath gd intl zip opcache; \
    \
    # Runtime deps minime + tool utili
    apk add --no-cache \
        git unzip \
        freetype libjpeg-turbo libpng libwebp icu-libs libzip; \
    \
    # Rimuovi i build-deps per alleggerire l'immagine
    apk del .build-deps; \
    rm -rf /var/cache/apk/*

# Composer (dal container ufficiale)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Utente non-root
RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel \
    && chown -R laravel:laravel /var/www/html
USER laravel
