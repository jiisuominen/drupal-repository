FROM almir/webhook
# We need php and git to build satis.
RUN apk add --update git php7 php7-openssl php7-common php7-json php7-phar php7-mbstring make

WORKDIR /root
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"

CMD ["-verbose", "-hooks=/etc/webhook/hooks.json", "-template"]
