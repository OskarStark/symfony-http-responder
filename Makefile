# vim: set tabstop=8 softtabstop=8 noexpandtab:
cs:
	docker run --rm -it -w /app -v ${PWD}:/app oskarstark/php-cs-fixer-ga:2.18.3

test:
	php vendor/bin/phpunit -v
