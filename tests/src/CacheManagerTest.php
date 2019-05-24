<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Cache;

use Ixocreate\Cache\CacheableInterface;
use Ixocreate\Cache\CacheManager;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class CacheManagerTest extends TestCase
{
    /**
     * @var CacheManager;
     */
    private $cacheManager;

    public function setUp()
    {
        $cacheItemPoolTrue = $this->createMock(CacheItemPoolInterface::class);
        $cacheItemPoolTrue->method('hasItem')->willReturn(true);
        $cacheItemPoolTrue->method('getItem')->willReturnCallback(function ($key) {
            $cacheItem = $this->createMock(CacheItemInterface::class);
            $cacheItem->method('get')->willReturn($key);
            return $cacheItem;
        });
        $cacheItemPoolTrue->method('save')->willReturn(true);

        $cacheItemPoolFalse = $this->createMock(CacheItemPoolInterface::class);
        $cacheItemPoolFalse->method('hasItem')->willReturn(false);
        $cacheItemPoolFalse->method('getItem')->willReturnCallback(function ($key) {
            $cacheItem = $this->createMock(CacheItemInterface::class);
            $cacheItem->method('get')->willReturn($key);
            return $cacheItem;
        });
        $cacheItemPoolFalse->method('save')->willReturn(false);

        $pool = [
            'fooTrue' => $cacheItemPoolTrue,
            'fooFalse' => $cacheItemPoolFalse,
        ];

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(function ($key) use ($pool) {
            return $pool[$key];
        });

        $this->cacheManager = new CacheManager($container);
    }

    public function testFetchWithForceFalseAndPositiveRetrieveResult()
    {
        $cacheable = $this->createMock(CacheableInterface::class);
        $cacheable->method('cacheName')->willReturn('fooTrue');
        $cacheable->method('cacheKey')->willReturn('fooKey');
        $cacheable->method('uncachedResult')->willReturnCallback(function () {
            return CacheableInterface::class . ' Callback!';
        });

        $this->assertSame('fooKey', $this->cacheManager->fetch($cacheable, false));
    }

    public function testFetchWithForceFalseAndNegativeRetrieveResult()
    {
        $cacheable = $this->createMock(CacheableInterface::class);
        $cacheable->method('cacheName')->willReturn('fooFalse');
        $cacheable->method('cacheKey')->willReturn('fooKey');
        $cacheable->method('uncachedResult')->willReturnCallback(function () {
            return CacheableInterface::class . ' Callback!';
        });

        $this->assertSame(
            CacheableInterface::class . ' Callback!',
            $this->cacheManager->fetch($cacheable, false)
        );
    }

    public function testFetchWithForceTrue()
    {
        $cacheable = $this->createMock(CacheableInterface::class);
        $cacheable->method('cacheName')->willReturn('fooTrue');
        $cacheable->method('cacheKey')->willReturn('fooKey');
        $cacheable->method('cacheTtl')->willReturn(1);
        $cacheable->method('uncachedResult')->willReturnCallback(function () {
            return CacheableInterface::class . ' Callback!';
        });

        $this->assertSame(
            CacheableInterface::class . ' Callback!',
            $this->cacheManager->fetch($cacheable, true)
        );
    }
}
