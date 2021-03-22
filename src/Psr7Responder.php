<?php

declare(strict_types=1);

/*
 * This file is part of oskarstark/symfony-http-responder.
 *
 * (c) Saif Eddin Gmati <azjezz@protonmail.com>
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OskarStark\Symfony\Http;

use Psr\Http\Message\ResponseInterface;
use SplFileInfo;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Twig\Error\Error as TwigError;

final class Psr7Responder
{
    public function __construct(
        private Responder $responder,
        private PsrHttpFactory $psrHttpFactory,
    ) {
    }

    /**
     * Create an empty response.
     *
     * @param array<string, string|list<string>> $headers
     */
    public function empty(int $status = Response::HTTP_NO_CONTENT, array $headers = []): ResponseInterface
    {
        return $this->psrHttpFactory->createResponse(
            $this->responder->empty($status, $headers)
        );
    }

    /**
     * Render the given Twig template and return an HTML response.
     *
     * @param array<mixed>                       $context
     * @param array<string, string|list<string>> $headers
     *
     * @throws TwigError
     */
    public function render(string $template, array $context = [], int $status = Response::HTTP_OK, array $headers = []): ResponseInterface
    {
        return $this->psrHttpFactory->createResponse(
            $this->responder->render($template, $context, $status, $headers)
        );
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param array<string, string|list<string>> $headers
     */
    public function redirect(string $url, int $status = Response::HTTP_FOUND, array $headers = []): ResponseInterface
    {
        return $this->psrHttpFactory->createResponse(
            $this->responder->redirect($url, $status, $headers)
        );
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param array<array-key, scalar>    $parameters
     * @param array<string, list<string>> $headers
     */
    public function route(string $route, array $parameters = [], int $status = Response::HTTP_FOUND, array $headers = []): ResponseInterface
    {
        return $this->psrHttpFactory->createResponse(
            $this->responder->route($route, $parameters, $status, $headers)
        );
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param array<string, string|list<string>> $headers
     * @param array<string, mixed>               $context
     */
    public function json(mixed $data, int $status = Response::HTTP_OK, array $headers = [], array $context = []): ResponseInterface
    {
        return $this->psrHttpFactory->createResponse(
            $this->responder->json($data, $status, $headers, $context)
        );
    }

    /**
     * Returns a BinaryFileResponse object with original or customized file name and disposition header.
     */
    public function file(SplFileInfo | string $file, ?string $filename = null, string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT): ResponseInterface
    {
        return $this->psrHttpFactory->createResponse(
            $this->responder->file($file, $filename, $disposition)
        );
    }
}
