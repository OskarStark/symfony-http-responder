# vim: set tabstop=8 softtabstop=8 noexpandtab:
.PHONY: phpstan
phpstan: vendor
	php vendor/bin/phpstan analyse -c phpstan.neon.dist

.PHONY: cs
cs: vendor
	php vendor/bin/php-cs-fixer fix --diff --diff-format=udiff --verbose

.PHONY: test
test: vendor
	php vendor/bin/phpunit -v

.PHONY: vendor
vendor: composer.json composer.lock
	symfony composer validate
	symfony composer install --no-interaction --no-progress --no-scripts
