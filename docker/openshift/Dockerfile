FROM ghcr.io/city-of-helsinki/drupal-repository:latest

COPY / /var/www/html/
COPY hooks.json /etc/webhook/hooks.json
WORKDIR /var/www/html
RUN make && \
  chmod a+rw $COMPOSER_HOME -R
