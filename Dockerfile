FROM ghcr.io/city-of-helsinki/drupal-repository:dev

RUN apk add --no-cache php81-xdebug
RUN { \
    echo '[xdebug]'; \
    echo 'zend_extension=xdebug.so'; \
    echo 'xdebug.mode=debug'; \
    echo 'xdebug.client_host=host.docker.internal'; \
    echo 'xdebug.idekey=PHPSTORM'; \
	} > /etc/php81/conf.d/50_xdebug.ini
