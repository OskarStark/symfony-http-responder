# vim: set tabstop=8 softtabstop=8 noexpandtab:
cs:
	php vendor/bin/php-cs-fixer fix --diff --diff-format=udiff --verbose

test:
	php vendor/bin/phpunit -v
