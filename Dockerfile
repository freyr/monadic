ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-cli-alpine
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && echo "pcov.directory=/app/src" > "$PHP_INI_DIR/conf.d/pcov.ini" \
    && pecl clear-cache \
    && apk del .build-deps \
    && rm -rf /tmp/pear
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_CS_FIXER_IGNORE_ENV=1
COPY --from=composer/composer:2-bin /composer /usr/bin/composer
WORKDIR /app
CMD sh -c "vendor/bin/phpunit"
