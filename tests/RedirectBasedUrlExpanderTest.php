<?php

declare (strict_types = 1);

namespace Pragmatist\UrlExpander;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use League\Uri\Schemes\Http;
use Pragmatist\UrlExpander\RedirectBasedUrlExpander\FailedToConnect;
use Pragmatist\UrlExpander\RedirectBasedUrlExpander\InvalidUri;
use Pragmatist\UrlExpander\RedirectBasedUrlExpander\TooManyRedirects;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class RedirectBasedUrlExpanderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockHandler
     */
    private $httpClientMockHandler;

    /**
     * @var RedirectBasedUrlExpander
     */
    private $expander;

    public function setUp()
    {
        $this->httpClientMockHandler = new MockHandler();

        $this->expander = RedirectBasedUrlExpander::createWithGuzzleClient(
            ['handler' => HandlerStack::create($this->httpClientMockHandler)]
        );
    }

    public function testCreateProducesUrlExpander()
    {
        $this->assertInstanceOf(UrlExpander::class, $this->expander);
    }

    public function testResolveReturnsAUri()
    {
        $givenUri = Http::createFromString('http://localhost');

        $this->httpClientShouldReturn(new Response(200));

        $this->assertInstanceOf(
            UriInterface::class,
            $this->expander->expand($givenUri)
        );
    }

    public function testResolvesUriToGivenWhenServerReturnsOk()
    {
        $givenUri = Http::createFromString('http://localhost');

        $this->httpClientShouldReturn(new Response(200));

        $this->assertEquals(
            $givenUri,
            $this->expander->expand($givenUri)
        );
    }

    public function testResolvesUriToRedirectedWhenServerRedirects()
    {
        $givenUri = Http::createFromString('http://localhost');

        $this->httpClientShouldReturn(new Response(301, ['Location' => 'http://localhost/redirected']));
        $this->httpClientShouldReturn(new Response(200));

        $this->assertEquals(
            (string) 'http://localhost/redirected',
            (string) $this->expander->expand($givenUri)
        );
    }

    public function testResolvesUriToRedirectedWhenMultipleRedirects()
    {
        $givenUri = Http::createFromString('http://localhost');

        $this->httpClientShouldReturn(new Response(301, ['Location' => 'http://localhost/redirected']));
        $this->httpClientShouldReturn(new Response(301, ['Location' => 'http://localhost/redirected-again']));
        $this->httpClientShouldReturn(new Response(200));

        $this->assertEquals(
            (string) 'http://localhost/redirected-again',
            (string) $this->expander->expand($givenUri)
        );
    }

    public function testResolvesUriToGivenWhenServerReturnsClientError()
    {
        $givenUri = Http::createFromString('http://localhost');

        $this->httpClientShouldReturn(new Response(404));

        $this->assertEquals(
            $givenUri,
            $this->expander->expand($givenUri)
        );
    }

    public function testResolvesUriToGivenWhenServerReturnsServerError()
    {
        $givenUri = Http::createFromString('http://localhost');

        $this->httpClientShouldReturn(new Response(500));

        $this->assertEquals(
            $givenUri,
            $this->expander->expand($givenUri)
        );
    }

    public function testThrowsExceptionWhenServerDoesTooManyRedirects()
    {
        $givenUri = Http::createFromString('http://localhost');

        $this->httpClientShouldReturn(new Response(301, ['Location' => 'http://localhost/redirected']));
        $this->httpClientShouldReturn(new Response(301, ['Location' => 'http://localhost/redirected']));
        $this->httpClientShouldReturn(new Response(301, ['Location' => 'http://localhost/redirected']));
        $this->httpClientShouldReturn(new Response(301, ['Location' => 'http://localhost/redirected']));
        $this->httpClientShouldReturn(new Response(301, ['Location' => 'http://localhost/redirected']));
        $this->httpClientShouldReturn(new Response(301, ['Location' => 'http://localhost/redirected']));
        $this->httpClientShouldReturn(new Response(200));

        $this->expectException(TooManyRedirects::class);
        $this->expander->expand($givenUri);
    }

    public function testThrowsExceptionWhenUriInvalid()
    {
        $givenUri = Http::createFromString('asdasdasd');

        $this->expectException(InvalidUri::class);
        $this->expander->expand($givenUri);
    }

    public function testThrowsExceptionWhenFailedToConnect()
    {
        $givenUri = Http::createFromString('http://localhost');

        $this->httpClientShouldReturn(
            new ConnectException('Failed to connect', new Request('HEAD', $givenUri))
        );

        $this->expectException(FailedToConnect::class);
        $this->expander->expand($givenUri);
    }

    private function httpClientShouldReturn($response)
    {
        $this->httpClientMockHandler->append($response);
    }
}
