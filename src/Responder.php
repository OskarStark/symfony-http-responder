<?php

declare(strict_types=1);

/**
 * This file is part of oskarstark/symfony-http-responder.
 *
 * (c) Saif Eddin Gmati <azjezz@protonmail.com>
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OskarStark\Symfony\Http;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;
use Twig\Error\Error as TwigError;

final class Responder
{
    public function __construct(
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator,
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * Create an empty response.
     *
     * @param array<string, list<string>|string> $headers
     */
    public function empty(int $status = Response::HTTP_NO_CONTENT, array $headers = []): Response
    {
        return new Response(null, $status, $headers);
    }

    /**
     * Render the given Twig template and return an HTML response.
     *
     * @param array<mixed>                       $context
     * @param array<string, list<string>|string> $headers
     *
     * @throws TwigError
     */
    public function render(string $template, array $context = [], int $status = Response::HTTP_OK, array $headers = []): Response
    {
        $content = $this->twig->render($template, $context);
        $response = new Response($content, $status, $headers);

        if (!$response->headers->has('Content-Type')) {
            $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        }

        return $response;
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param array<string, list<string>|string> $headers
     */
    public function redirect(string $url, int $status = Response::HTTP_FOUND, array $headers = []): RedirectResponse
    {
        return new RedirectResponse($url, $status, $headers);
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param array<array-key, scalar>    $parameters
     * @param array<string, list<string>> $headers
     */
    public function route(string $route, array $parameters = [], int $status = Response::HTTP_FOUND, array $headers = []): RedirectResponse
    {
        $url = $this->urlGenerator->generate($route, $parameters);

        return $this->redirect($url, $status, $headers);
    }

    /**
     * Returns a Response with the given content, status code and headers.
     *
     * @param array<string, string> $headers
     */
    public function response(?string $content = '', int $status = Response::HTTP_FOUND, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param array<string, list<string>|string> $headers
     * @param array<string, mixed>               $context
     */
    public function json(mixed $data, int $status = Response::HTTP_OK, array $headers = [], array $context = []): JsonResponse
    {
        $json = $this->serializer->serialize($data, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], $context));

        return new JsonResponse($json, $status, $headers, true);
    }

    /**
     * Returns a BinaryFileResponse object with original or customized file name and disposition header.
     */
    public function file(\SplFileInfo|string $file, ?string $filename = null, string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file);

        $filename ??= $response->getFile()->getFilename();
        $response->setContentDisposition($disposition, $filename);

        return $response;
    }

     /**
      * Returns the generated Url for the given route and parameters (absolute URL by default).
      *
     * @param array<array-key, scalar> $parameters
     */
    public function url(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_URL): string
    {
        return $this->urlGenerator->generate($route, $parameters, $referenceType);
    }
}
