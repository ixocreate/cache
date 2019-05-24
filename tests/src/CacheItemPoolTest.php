<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Cache;

use Ixocreate\Cache\CacheItemPool;
use Ixocreate\Cache\ResetableInterface;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\PruneableInterface;
use Symfony\Component\Cache\ResettableInterface;
use Symfony\Contracts\Service\ResetInterface;

class CacheItemPoolTest extends TestCase
{
    /**
     * @var CacheItemPool
     */
    private $cacheItemPool;

    public function setUp()
    {
        $items = [
            'foo' => $this->createMock(CacheItemInterface::class),
            'bar' => $this->createMock(CacheItemInterface::class),
        ];

        $cacheItemPool = $this->createMock(CacheItemPoolInterface::class);
        $cacheItemPool->method('getItem')->willReturnCallback(function ($key) use ($items) {
            return $items[$key];
        });
        $cacheItemPool->method('getItems')->willReturnCallback(function ($keys) use ($items) {
            return $items;
        });
        $cacheItemPool->method('hasItem')->willReturnCallback(function ($key) use ($items) {
            if (\array_key_exists($key, $items)) {
                return true;
            }
            return false;
        });
        $cacheItemPool->method('clear')->willReturn(true);
        $cacheItemPool->method('deleteItem')->willReturn(true);
        $cacheItemPool->method('deleteItems')->willReturn(true);
        $cacheItemPool->method('save')->willReturn(true);
        $cacheItemPool->method('saveDeferred')->willReturn(true);
        $cacheItemPool->method('commit')->willReturn(true);

        $this->cacheItemPool = new CacheItemPool($cacheItemPool);
    }

    public function testInnerPool()
    {
        $this->assertInstanceOf(CacheItemPoolInterface::class, $this->cacheItemPool->innerPool());
    }

    public function testGetItem()
    {
        $this->assertInstanceOf(CacheItemInterface::class, $this->cacheItemPool->getItem('foo'));
    }

    public function testGetItems()
    {
        $keys = [
            'foo',
            'bar',
        ];

        $this->assertIsArray($this->cacheItemPool->getItems($keys));
    }

    public function testHasItem()
    {
        $this->assertIsBool($this->cacheItemPool->hasItem('foo'));
    }

    public function testClear()
    {
        $this->assertIsBool($this->cacheItemPool->clear());
    }

    public function testDeleteItem()
    {
        $this->assertIsBool($this->cacheItemPool->deleteItem('foo'));
    }

    public function testDeleteItems()
    {
        $this->assertIsBool($this->cacheItemPool->deleteItems(['foo','bar']));
    }

    public function testSave()
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);

        $this->assertIsBool($this->cacheItemPool->save($cacheItem));
    }

    public function testSaveDeferred()
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);

        $this->assertIsBool($this->cacheItemPool->saveDeferred($cacheItem));
    }

    public function testCommit()
    {
        $this->assertIsBool($this->cacheItemPool->commit());
    }

    public function testPrune()
    {
        $symfonyPrune = $this->createMock([CacheItemPoolInterface::class, PruneableInterface::class]);
        $symfonyPrune->method('prune');

        $ixoPrune = $this->createMock([CacheItemPoolInterface::class ,\Ixocreate\Cache\PruneableInterface::class]);
        $ixoPrune->method('prune');

        $symfonyCacheItemPool = new CacheItemPool($symfonyPrune);

        $ixoCacheItemPool = new CacheItemPool($ixoPrune);

        $this->assertNull($symfonyCacheItemPool->prune());

        $this->assertNull($ixoCacheItemPool->prune());
    }

    public function testReset()
    {
        $symfonyResettable = $this->createMock([CacheItemPoolInterface::class, ResettableInterface::class]);
        $symfonyReset = $this->createMock([CacheItemPoolInterface::class, ResetInterface::class]);
        $ixoReset = $this->createMock([CacheItemPoolInterface::class, ResetableInterface::class]);

        $symfonyResettableCacheItemPool = new CacheItemPool($symfonyResettable);
        $symfonyResetCacheItemPool = new CacheItemPool($symfonyReset);
        $ixoResetCacheItemPool = new CacheItemPool($ixoReset);

        $this->assertNull($symfonyResettableCacheItemPool->reset());
        $this->assertNull($symfonyResetCacheItemPool->reset());
        $this->assertNull($ixoResetCacheItemPool->reset());
    }
}
