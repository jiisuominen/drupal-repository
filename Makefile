-include .env

DOCKER_COMPOSE_V2 := $(shell docker compose > /dev/null 2>&1 && echo "yes" || echo "no")

ifeq ($(DOCKER_COMPOSE_V2),yes)
	DOCKER_COMPOSE_CMD := docker compose
else
	DOCKER_COMPOSE_CMD := docker-compose
endif

PHP := /usr/bin/php -dmemory_limit=-1
GIT := /usr/bin/git
COMPOSER := $(shell which composer.phar 2>/dev/null || which composer 2>/dev/null)
COMPOSER_AUTH = ${HOME}/.composer/auth.json

.PHONY := update-repository docker-build docker-attach dist

default: dist

docker-attach:
	$(DOCKER_COMPOSE_CMD) exec app sh

update-repository:
	$(GIT) checkout .
	$(GIT) clean -f
	$(GIT) pull

$(COMPOSER_AUTH):
	composer -g config github-oauth.github.com ${GITHUB_OAUTH}

dist: $(COMPOSER_AUTH)
	$(PHP) $(COMPOSER) install --no-progress --profile --prefer-dist --no-interaction --no-dev --optimize-autoloader
