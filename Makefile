#!/usr/bin/make -f

PROCESSORS_NUM := $(shell getconf _NPROCESSORS_ONLN)
GLOBAL_CONFIG := -d memory_limit=-1

# -----------------------------------------------------------------------------


.PHONY: all
all: check test

.PHONY: clean
clean:
	rm -rf ./build

.PHONY: clean-all
clean-all: clean
	rm -rf ./vendor
	rm -rf ./composer.lock

.PHONY: check
check:
	mkdir -p build
	php ${GLOBAL_CONFIG} vendor/bin/phpcs --parallel=${PROCESSORS_NUM} --report-junit=build/phpcs.xml

.PHONY: test
test: clean
	php -d xdebug.mode=coverage vendor/bin/phpunit

.PHONY: coverage
coverage: test
	@if [ "`uname`" = "Darwin" ]; then open build/coverage/index.html; fi
