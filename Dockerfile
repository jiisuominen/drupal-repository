FROM ghcr.io/city-of-helsinki/drupal-repository:latest

RUN apk add --no-cache php8-xdebug
RUN { \
    echo '[xdebug]'; \
    echo 'zend_extension=xdebug.so'; \
    echo 'xdebug.mode=debug'; \
    echo 'xdebug.client_host=host.docker.internal'; \
    echo 'xdebug.idekey=PHPSTORM'; \
	} > /etc/php8/conf.d/50_xdebug.ini
