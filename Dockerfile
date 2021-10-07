FROM almir/webhook
# We need php and git to build satis.
RUN apk add --update git php7 php7-openssl php7-common php7-json php7-phar php7-mbstring make

WORKDIR /root
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

CMD ["-verbose", "-hooks=/etc/webhook/hooks.json", "-template"]
