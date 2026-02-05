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
use PHPUnit\Framework\Attributes\Test;
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
    private MockObject $serializer;
    private MockObject $urlGenerator;
    private MockObject $psrHttpFactory;
    private Psr7Responder $psr7Responder;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->psrHttpFactory = $this->createMock(PsrHttpFactory::class);
        $responder = new Responder($this->createMock(Environment::class), $this->urlGenerator, $this->serializer);
        $this->psr7Responder = new Psr7Responder($responder, $this->psrHttpFactory);
    }

    #[Test]
    public function empty(): void
    {
        $this->psrHttpFactory->expects($this->once())
            ->method('createResponse')
            ->with(self::isInstanceOf(Response::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->empty();
    }

    #[Test]
    public function render(): void
    {
        $this->psrHttpFactory->expects($this->once())
            ->method('createResponse')
            ->with(self::isInstanceOf(Response::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->render(
            'error.html.twig',
            ['message' => 'Not Found!'],
            Response::HTTP_NOT_FOUND,
        );
    }

    #[Test]
    public function redirect(): void
    {
        $this->psrHttpFactory->expects($this->once())
            ->method('createResponse')
            ->with(self::isInstanceOf(RedirectResponse::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->redirect('/user/kpicaza');
    }

    #[Test]
    public function route(): void
    {
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('user_profile', ['username' => 'kpicaza'])
            ->willReturn('/user/kpicaza');
        $this->psrHttpFactory->expects($this->once())
            ->method('createResponse')
            ->with(self::isInstanceOf(RedirectResponse::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->route('user_profile', [
            'username' => 'kpicaza',
        ]);
    }

    #[Test]
    public function response(): void
    {
        $this->psrHttpFactory->expects($this->once())
            ->method('createResponse')
            ->with(self::isInstanceOf(Response::class))
            ->willReturn($this->createMock(ResponseInterface::class));

        $this->psr7Responder->response('some content', Response::HTTP_OK, [
            'Some-Header' => 'some value',
        ]);
    }

    #[Test]
    public function json(): void
    {
        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with(['title' => 'Hello, World!'], 'json', [
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            ])
            ->willReturn('{"title": "Hello, World!"}');
        $this->psrHttpFactory->expects($this->once())
            ->method('createResponse')
            ->with(self::isInstanceOf(JsonResponse::class))
            ->willReturn($this->createMock(ResponseInterface::class));

        $this->psr7Responder->json(['title' => 'Hello, World!']);
    }

    #[Test]
    public function file(): void
    {
        $this->psrHttpFactory->expects($this->once())
            ->method('createResponse')
            ->with(self::isInstanceOf(BinaryFileResponse::class))
            ->willReturn($this->createMock(ResponseInterface::class));
        $this->psr7Responder->file(__FILE__);
    }
}
