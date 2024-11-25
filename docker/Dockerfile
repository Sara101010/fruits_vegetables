FROM php:8.1-fpm-alpine3.15

ARG APP_ENV

RUN apk add --no-cache \
    git \
    icu-dev \
    zlib-dev \
    libzip-dev \
    bash \
    && apk add --no-cache --virtual .build-deps gcc g++ make \
    && docker-php-ext-install pdo_mysql intl zip bcmath \
    && apk del .build-deps

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions @composer \
    && rm /usr/local/bin/install-php-extensions

WORKDIR /app