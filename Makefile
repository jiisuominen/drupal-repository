PHP := php -dmemory_limit=-1
SATIS := vendor/bin/satis
COMPOSER := $(shell which composer.phar 2>/dev/null || which composer 2>/dev/null)

.PHONY := all dist/packages.json

all: dist/packages.json

dist/packages.json: dist/.git $(SATIS) Makefile satis.json
		$(PHP) $(SATIS) build satis.json dist

dist/.git:
		git clone git@github.com:City-of-Helsinki/drupal-repository.git dist -b gh-pages --depth=1

$(SATIS): composer.lock
		$(PHP) $(COMPOSER) install
		touch $@
