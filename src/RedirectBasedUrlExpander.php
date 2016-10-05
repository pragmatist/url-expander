<?php

declare (strict_types = 1);

namespace Pragmatist\UrlExpander;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use League\Uri\Schemes\Http;
use Pragmatist\UrlExpander\RedirectBasedUrlExpander\FailedToConnect;
use Pragmatist\UrlExpander\RedirectBasedUrlExpander\InvalidUri;
use Pragmatist\UrlExpander\RedirectBasedUrlExpander\TooManyRedirects;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class RedirectBasedUrlExpander implements UrlExpander
{
    /**
     * Default client options.
     *
     * @var array
     */
    private static $defaultClientOptions = [
        'http_errors' => false,
        'allow_redirects' => ['track_redirects' => true]
    ];

    /**
     * @var ClientInterface
     */
    private $httpClient;

    public static function createWithGuzzleClient(array $clientOptions = [])
    {
        return new RedirectBasedUrlExpander(
            new Client(array_merge(static::$defaultClientOptions, $clientOptions))
        );
    }

    public function expand(UriInterface $uri): UriInterface
    {
        $response = $this->fetchUri($uri);

        if (!$this->hasTrackedRedirects($response)) {
            return $uri;
        }

        return $this->getUriOfLastRedirect($response);
    }

    private function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    private function fetchUri(UriInterface $uri): ResponseInterface
    {
        try {
            return $this->httpClient->request('HEAD', $uri);
        } catch (TooManyRedirectsException $e) {
            throw new TooManyRedirects($e->getMessage());
        } catch (ConnectException $e) {
            throw new FailedToConnect($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new InvalidUri($e->getMessage());
        }
    }

    private function hasTrackedRedirects(ResponseInterface $response): bool
    {
        return $response->hasHeader('X-Guzzle-Redirect-History');
    }

    private function getUriOfLastRedirect(ResponseInterface $response): UriInterface
    {
        $redirectHistory = $response->getHeader('X-Guzzle-Redirect-History');
        return Http::createFromString(array_pop($redirectHistory));
    }
}
