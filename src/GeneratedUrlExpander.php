<?php

declare (strict_types = 1);

namespace Pragmatist\UrlExpander;

use Faker\Generator;
use League\Uri\Schemes\Http;
use Psr\Http\Message\UriInterface;

/**
 * A URL expander that returns a URL from a Faker Generator.
 */
final class GeneratedUrlExpander implements UrlExpander
{
    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function expand(UriInterface $uri): UriInterface
    {
        return Http::createFromString(
            $this->generator->url
        );
    }
}
