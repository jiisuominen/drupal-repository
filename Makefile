PHP := php -dmemory_limit=-1
SATIS := vendor/bin/satis
COMPOSER := $(shell which composer.phar 2>/dev/null || which composer 2>/dev/null)

.PHONY := update-repository docker-build docker-attach

all: update-repository dist

docker-build:
	docker-compose build --no-cache
	docker-compose stop
	docker-compose up -d

docker-attach:
	docker-compose exec app sh

update-repository:
	git pull

dist:
	composer -g config github-oauth.github.com ${GITHUB_OAUTH}
	$(PHP) $(SATIS)

$(SATIS): composer.lock
	$(PHP) $(COMPOSER) install
	touch $@
