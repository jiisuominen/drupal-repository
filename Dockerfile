FROM        golang:alpine3.14 AS build
WORKDIR     /go/src/github.com/adnanh/webhook
ENV         WEBHOOK_VERSION 2.8.0
RUN         apk add --update -t build-deps curl libc-dev gcc libgcc
RUN         curl -L --silent -o webhook.tar.gz https://github.com/adnanh/webhook/archive/${WEBHOOK_VERSION}.tar.gz && \
            tar -xzf webhook.tar.gz --strip 1 &&  \
            go get -d && \
            go build -o /usr/local/bin/webhook && \
            apk del --purge build-deps && \
            rm -rf /var/cache/apk/* && \
            rm -rf /go

FROM        alpine:3.14
COPY        --from=build /usr/local/bin/webhook /usr/local/bin/webhook
WORKDIR     /etc/webhook
VOLUME      ["/etc/webhook"]
EXPOSE      9000
ENTRYPOINT  ["/usr/local/bin/webhook"]

# We need php and git to build satis.
RUN         apk add --update git php8 php8-openssl php8-common php8-json php8-phar php8-mbstring make
RUN         ln -s /usr/bin/php8 /usr/bin/php

WORKDIR     /root
RUN         php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN         php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN         php composer-setup.php --install-dir=/bin --filename=composer
RUN         php -r "unlink('composer-setup.php');"

CMD         ["-verbose", "-hooks=/etc/webhook/hooks.json", "-template", "-hotreload", "-debug"]
