-include .env

PHP := /usr/bin/php -dmemory_limit=-1
GIT := /usr/bin/git
COMPOSER := $(shell which composer.phar 2>/dev/null || which composer 2>/dev/null)
COMPOSER_AUTH = ${COMPOSER_HOME}/auth.json

default: vendor/composer/installed.php

$(COMPOSER_AUTH):
	composer -g config github-oauth.github.com ${GITHUB_OAUTH}

vendor/composer/installed.php: $(COMPOSER_AUTH)
	$(PHP) $(COMPOSER) install --no-progress --profile --prefer-dist --no-interaction --no-dev --optimize-autoloader
