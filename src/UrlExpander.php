<?php

declare (strict_types = 1);

namespace Pragmatist\UrlExpander;

use Psr\Http\Message\UriInterface;

interface UrlExpander
{
    /**
     * Expand a given short URL into the long URL.
     */
    public function expand(UriInterface $uri): UriInterface;
}
