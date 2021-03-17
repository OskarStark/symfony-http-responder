# symfony-http-responder

This library provides a Symfony responder class, which can be used to render a template, return json or a file and redirect to route/url.

[![CI][ci_badge]][ci_link]

This library is designed to be used in controllers which does not extend from AbstractController.

## Installation

```
composer require oskarstark/symfony-http-responder
```

## Usage

### Render a Template

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use OskarStark\Symfony\Http\Responder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/index', name: 'app_index')]
final class IndexController
{
    public function __construct(
        private Responder $responder,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->responder->render('index.html.twig');
    }
}
```

### Return JSON

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use OskarStark\Symfony\Http\Responder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'app_api')]
final class ApiController
{
    public function __construct(
        private Responder $responder,
    ) {
    }

    public function __invoke(): Response
    {
        $data = [
            'foo' => 42,
        ];
    
        return $this->responder->json($data);
    }
}
```

### Return a File

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use OskarStark\Symfony\Http\Responder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/download', name: 'app_download')]
final class DownloadController
{
    public function __construct(
        private Responder $responder,
    ) {
    }

    public function __invoke(): Response
    {
        // You can either provide a filepath
        $file = '/app/invoices/invoice.pdf';
        
        // or an SplFileObject
        $file = new \SplFileObject('/app/invoices/invoice.pdf')M
        
        return $this->responder->file($file);
    }
}
```

### Redirect to Url

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use OskarStark\Symfony\Http\Responder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/redirect', name: 'app_redirect')]
final class RedirectController
{
    public function __construct(
        private Responder $responder,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->responder->redirect('http://google.com');
    }
}
```

### Redirect to Route

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use OskarStark\Symfony\Http\Responder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/redirect', name: 'app_redirect')]
final class RedirectController
{
    public function __construct(
        private Responder $responder,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->responder->route('app_my_route');
    }
}
```

[ci_badge]: https://github.com/OskarStark/symfony-http-responder/workflows/CI/badge.svg?branch=main
[ci_link]: https://github.com/OskarStark/symfony-http-responder/actions?query=workflow:ci+branch:main
