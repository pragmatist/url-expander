<?php

declare (strict_types = 1);

namespace Pragmatist\UrlExpander;

use League\Uri\Schemes\Http;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\UriInterface;

final class CachingUrlExpander implements UrlExpander
{
    /**
     * @var UrlExpander
     */
    private $expander;

    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    public function __construct(UrlExpander $expander, CacheItemPoolInterface $cacheItemPool)
    {
        $this->expander = $expander;
        $this->cacheItemPool = $cacheItemPool;
    }

    public function expand(UriInterface $uri): UriInterface
    {
        if (!$this->isCached($uri)) {
            $this->addToCache($uri, $this->expander->expand($uri));
        }

        return $this->getFromCache($uri);
    }

    private function isCached(UriInterface $uri): bool
    {
        return $this->getCacheItem($uri)->isHit();
    }

    private function addToCache(UriInterface $shortUri, UriInterface $longUri)
    {
        $this->cacheItemPool->save(
            $this->getCacheItem($shortUri)->set((string) $longUri)
        );
    }

    private function getFromCache(UriInterface $uri): UriInterface
    {
        return Http::createFromString($this->getCacheItem($uri)->get());
    }

    private function getCacheItem(UriInterface $uri): CacheItemInterface
    {
        return $this->cacheItemPool->getItem($this->cacheKeyForUri($uri));
    }

    private function cacheKeyForUri(UriInterface $uri): string
    {
        return sha1((string) $uri);
    }
}
