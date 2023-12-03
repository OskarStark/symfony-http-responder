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

namespace OskarStark\Symfony\Http\Tests;

use OskarStark\Symfony\Http\Psr7Responder;
use OskarStark\Symfony\Http\Responder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

final class Psr7ResponderTest extends TestCase
{
    private MockObject $twig;
    private MockObject $serializer;
    private MockObject $urlGenerator;
    private MockObject $psrHttpFactory;
    private Responder $responder;
    private Psr7Responder $psr7Responder;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->psrHttpFactory = $this->createMock(PsrHttpFactory::class);
        $this->responder = new Responder($this->twig, $this->urlGenerator, $this->serializer);
        $this->psr7Responder = new Psr7Responder($this->responder, $this->psrHttpFactory);
    }

    public function testEmpty(): void
    {
        $this->psrHttpFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::isInstanceOf(Response::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->empty();
    }

    public function testRender(): void
    {
        $this->psrHttpFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::isInstanceOf(Response::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->render(
            'error.html.twig',
            ['message' => 'Not Found!'],
            Response::HTTP_NOT_FOUND,
        );
    }

    public function testRedirect(): void
    {
        $this->psrHttpFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::isInstanceOf(RedirectResponse::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->redirect('/user/kpicaza');
    }

    public function testRoute(): void
    {
        $this->urlGenerator
            ->expects(self::once())
            ->method('generate')
            ->with('user_profile', ['username' => 'kpicaza'])
            ->willReturn('/user/kpicaza');
        $this->psrHttpFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::isInstanceOf(RedirectResponse::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->route('user_profile', [
            'username' => 'kpicaza',
        ]);
    }

    public function testResponse(): void
    {
        $this->psrHttpFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::isInstanceOf(Response::class))
            ->willReturn($this->createMock(ResponseInterface::class));

        $this->psr7Responder->response('some content', Response::HTTP_OK, [
            'Some-Header' => 'some value',
        ]);
    }

    public function testJson(): void
    {
        $this->serializer
            ->expects(self::once())
            ->method('serialize')
            ->with(['title' => 'Hello, World!'], 'json', [
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            ])
            ->willReturn('{"title": "Hello, World!"}');
        $this->psrHttpFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::isInstanceOf(JsonResponse::class))
            ->willReturn($this->createMock(ResponseInterface::class));

        $this->psr7Responder->json(['title' => 'Hello, World!']);
    }

    public function testFile(): void
    {
        $this->psrHttpFactory->expects(self::once())
            ->method('createResponse')
            ->with(self::isInstanceOf(BinaryFileResponse::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->file(__FILE__);
    }
}
