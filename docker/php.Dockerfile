FROM demidovich/php-fpm:8.1-alpine

ARG UID=82
ARG GID=82

ENV UID=${UID:-82} \
    GID=${GID:-82} \
    CONTAINER_ENVIRONMENT=${CONTAINER_ENVIRONMENT:-production} \
    PHP_COMPOSER_VERSION=2.5.4

RUN set -eux; \
    if [ $UID -ne 82 ]; then \
        usermod -u ${UID} www-data; \
    fi; \
    # macos fix
    # if [ $GID -ne 82 ]; then \
    if [ $GID -ne 82 ] && ! grep -q :${GID}: /etc/group; then \
        groupmod -g ${GID} www-data; \
    fi; \
    if [ "$CONTAINER_ENVIRONMENT" != "production" ]; then \
        CONTAINER_ENVIRONMENT="development"; \
    fi; \
    cp -f "$PHP_INI_DIR/php.ini-$CONTAINER_ENVIRONMENT" "$PHP_INI_DIR/php.ini"; \
    #
    # composer
    #
    install-composer.sh $PHP_COMPOSER_VERSION; \
    chown -R www-data:www-data /composer

USER "www-data"

WORKDIR /app
