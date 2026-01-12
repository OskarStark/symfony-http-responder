# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Symfony library that provides responder classes (`Responder` and `Psr7Responder`) for controllers that don't extend from `AbstractController`. The library offers convenient methods to:
- Render Twig templates
- Return JSON responses (using Symfony Serializer)
- Return file downloads (BinaryFileResponse)
- Create redirects (to URLs or named routes)
- Create empty responses

The library supports both standard Symfony HttpFoundation responses and PSR-7/PSR-15 responses via the `Psr7Responder` class.

## Development Commands

Install dependencies:
```bash
symfony composer validate
symfony composer install --no-interaction --no-progress --no-scripts
```

Run all tests:
```bash
make test
# or directly:
php vendor/bin/phpunit
```

Run specific test:
```bash
php vendor/bin/phpunit --filter testMethodName
# or with path:
php vendor/bin/phpunit tests/ResponderTest.php
```

Run static analysis:
```bash
make phpstan
# or directly:
php vendor/bin/phpstan analyse -c phpstan.neon.dist
```

Run code style fixer:
```bash
make cs
# or directly:
php vendor/bin/php-cs-fixer fix --diff --verbose
```

## Code Architecture

### Core Classes

- `src/Responder.php` - Main responder class that wraps Symfony HttpFoundation components (Twig, UrlGenerator, Serializer)
- `src/Psr7Responder.php` - PSR-7 adapter that delegates to `Responder` and converts responses using `PsrHttpFactory`

Both classes provide the same methods (`render`, `json`, `file`, `redirect`, `route`, `response`, `empty`), but return different response types:
- `Responder` returns Symfony `Response` objects
- `Psr7Responder` returns PSR-7 `ResponseInterface` objects

### Testing

- Tests use PHPUnit 10.5 with `#[Test]` attributes
- Test files mirror the source structure: `tests/ResponderTest.php` and `tests/Psr7ResponderTest.php`

## PHP Standards

- PHP 8.2+ required
- Uses `declare(strict_types=1)` in all files
- Uses ergebnis/php-cs-fixer-config with Php82 ruleset
- PHPStan level: max
- All classes are `final`
- File headers with copyright information are required

## Symfony Compatibility

The library supports Symfony 6.4, 7.x, and 8.x.
