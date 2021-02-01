<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Cache;

use Ixocreate\Cache\CacheItemPool;
use Ixocreate\Misc\Cache\IxocreateCacheItemPoolInterface;
use Ixocreate\Misc\Cache\SymfonyCacheItemPoolInterface;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPoolTest extends TestCase
{
    /**
     * @var CacheItemPool
     */
    private $cacheItemPool;

    public function setUp(): void
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
        $symfonyPrune = $this->createMock(SymfonyCacheItemPoolInterface::class);
        $symfonyPrune->expects($this->once())->method('prune');

        $ixoPrune = $this->createMock(IxocreateCacheItemPoolInterface::class);
        $ixoPrune->expects($this->once())->method('prune')->willReturnCallback(function () {
        });

        $symfonyCacheItemPool = new CacheItemPool($symfonyPrune);
        $ixoCacheItemPool = new CacheItemPool($ixoPrune);

        $symfonyCacheItemPool->prune();
        $ixoCacheItemPool->prune();
    }

    public function testReset()
    {
        $symfonyResettable = $this->createMock(SymfonyCacheItemPoolInterface::class);
        $symfonyResettable->expects($this->once())->method('reset');

        $ixoReset = $this->createMock(IxocreateCacheItemPoolInterface::class);
        $ixoReset->expects($this->once())->method('reset');

        $symfonyResettableCacheItemPool = new CacheItemPool($symfonyResettable);
        $ixoResetCacheItemPool = new CacheItemPool($ixoReset);

        $symfonyResettableCacheItemPool->reset();
        $ixoResetCacheItemPool->reset();
    }
}
