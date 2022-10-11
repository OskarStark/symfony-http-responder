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

namespace OskarStark\Symfony\Http\Tests;

use OskarStark\Symfony\Http\Responder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

final class ResponderTest extends TestCase
{
  private MockObject $twig;
  private MockObject $serializer;
  private MockObject $urlGenerator;
  private Responder $responder;

  protected function setUp(): void
  {
    $this->twig = $this->createMock(Environment::class);
    $this->serializer = $this->createMock(SerializerInterface::class);
    $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

    $this->responder = new Responder($this->twig, $this->urlGenerator, $this->serializer);
  }

  public function testEmpty(): void
  {
    $response = $this->responder->empty();

    self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
  }

  public function testEmptyStatus(): void
  {
    $response = $this->responder->empty(Response::HTTP_INTERNAL_SERVER_ERROR, [
      'X-Error-Identifier' => 'XYZ'
    ]);

    self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    self::assertSame('XYZ', $response->headers->get('X-Error-Identifier'));
  }

  public function testRender(): void
  {
    $this->twig
      ->expects($this->once())
      ->method('render')
      ->with('error.html.twig', ['message' => 'Not Found!'])
      ->willReturn('Not Found!');

    $response = $this->responder
      ->render('error.html.twig', ['message' => 'Not Found!'], Response::HTTP_NOT_FOUND);

    self::assertSame('Not Found!', $response->getContent());
    self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    self::assertSame('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
  }

  public function testRenderWithContentType(): void
  {
    $this->twig
      ->expects($this->once())
      ->method('render')
      ->with('content.json.twig', [])
      ->willReturn('{}');

    $response = $this->responder
      ->render('content.json.twig', [], Response::HTTP_OK, [
        'Content-Type' => 'application/json'
      ]);

    self::assertSame('{}', $response->getContent());
    self::assertSame('application/json', $response->headers->get('Content-Type'));
    self::assertSame(Response::HTTP_OK, $response->getStatusCode());
  }

  public function testRedirect(): void
  {
    $response = $this->responder->redirect('/user/azjezz');

    self::assertSame('/user/azjezz', $response->headers->get('Location'));
    self::assertSame(Response::HTTP_FOUND, $response->getStatusCode());
  }

  public function testResponse(): void
  {
    $response = $this->responder->response('some content', Response::HTTP_FORBIDDEN, [
        'Content-Type' => 'text/plain'
    ]);

    self::assertSame('some content', $response->getContent());
    self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    self::assertSame('text/plain', $response->headers->get('Content-Type'));
  }

  public function testRoute(): void
  {
    $this->urlGenerator
      ->expects($this->once())
      ->method('generate')
      ->with('user_profile', ['username' => 'azjezz'])
      ->willReturn('/user/azjezz');

    $response = $this->responder->route('user_profile', [
      'username' => 'azjezz'
    ]);

    self::assertSame('/user/azjezz', $response->headers->get('Location'));
    self::assertSame(Response::HTTP_FOUND, $response->getStatusCode());
  }

  public function testJson(): void
  {
    $this->serializer
      ->expects($this->once())
      ->method('serialize')
      ->with(['title' => 'Hello, World!'], 'json', [
        'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
      ])
      ->willReturn('{"title": "Hello, World!"}');

    $response = $this->responder->json(['title' => 'Hello, World!']);

    self::assertSame('{"title": "Hello, World!"}', $response->getContent());
  }

  public function testFileDefaultName(): void
  {
    $response = $this->responder->file(__FILE__);

    self::assertSame('attachment; filename=ResponderTest.php', $response->headers->get('Content-Disposition'));
  }

  public function testFileAttachment(): void
  {
    $response = $this->responder->file(__FILE__, 'invoice.pdf');

    self::assertSame('attachment; filename=invoice.pdf', $response->headers->get('Content-Disposition'));
  }

  public function testFileInline(): void
  {
    $response = $this->responder->file(__FILE__, 'invoice.pdf', ResponseHeaderBag::DISPOSITION_INLINE);

    self::assertSame('inline; filename=invoice.pdf', $response->headers->get('Content-Disposition'));
  }
}
