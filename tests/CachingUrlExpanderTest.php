<?php

declare (strict_types = 1);

namespace Pragmatist\UrlExpander;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Faker\Factory;
use League\Uri\Schemes\Http;

final class CachingUrlExpanderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CachingUrlExpander
     */
    private $expander;

    public function setUp()
    {
        $this->expander = new CachingUrlExpander(
            new GeneratedUrlExpander(Factory::create()),
            new ArrayCachePool()
        );
    }

    public function testCreateProducesUrlExpander()
    {
        $this->assertInstanceOf(UrlExpander::class, $this->expander);
    }

    public function testExpandProxiesToInternalExpander()
    {
        $uri = (string) $this->expander->expand(Http::createFromString('http://pragmatist.nl'));

        $this->assertNotEmpty($uri);
        $this->assertNotEquals('http://pragmatist.nl', $uri);
    }

    public function testExpandCachesConsecutiveSameCalls()
    {
        $givenUri = Http::createFromString('http://pragmatist.nl');

        $uriOne = $this->expander->expand($givenUri);
        $uriTwo = $this->expander->expand($givenUri);

        $this->assertEquals($uriOne, $uriTwo);
    }

    public function testExpandDoesNotCacheConsecutiveDifferentCalls()
    {
        $uriOne = $this->expander->expand(Http::createFromString('http://pragmatist.nl'));
        $uriTwo = $this->expander->expand(Http::createFromString('http://google.com'));
        $uriThree = $this->expander->expand(Http::createFromString('http://pragmatist.nl'));

        $this->assertEquals($uriOne, $uriThree);
        $this->assertNotEquals($uriOne, $uriTwo);
        $this->assertNotEquals($uriThree, $uriTwo);
    }
}
