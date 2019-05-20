<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Cache;

use Ixocreate\Cache\Cache;
use Ixocreate\Cache\PruneableInterface;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheTest extends TestCase
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    public function setUp()
    {
        $items = [
            'foo' => 'fooValue',
            'bar' => 'barValue',

        ];

        $this->cacheItemPool = $this->createMock(CacheItemPoolInterface::class);
        $this->cacheItemPool->method('save')->willReturn(true);
        $this->cacheItemPool->method('commit')->willReturn(true);
        $this->cacheItemPool->method('clear')->willReturn(true);
        $this->cacheItemPool->method('hasItem')->willReturnCallback(function ($key) use ($items) {
            if (\array_key_exists($key, $items)) {
                return true;
            }
            return false;
        });

        $this->cacheItemPool->method('getItem')->willReturnCallback(function ($key) use ($items) {
            $cacheItem = $this->createMock(CacheItemInterface::class);
            $cacheItem->method('get')->willReturnCallback(function () use ($items, $key) {
                if (\array_key_exists($key, $items)) {
                    return $items[$key];
                }
            });
            $cacheItem->method('set');
            $cacheItem->method('expiresAfter');
            return $cacheItem;
        });
        $this->cacheItemPool->method('deleteItem')->willReturnCallback(function ($key) use ($items) {
            if (\array_key_exists($key, $items)) {
                return true;
            }
            return false;
        });
        $this->cacheItemPool->method('saveDeferred')->willReturn(true);
    }

    public function testPool()
    {
        $cache = new Cache($this->cacheItemPool);

        $this->assertInstanceOf(CacheItemPoolInterface::class, $cache->pool());
    }

    public function testPositiveGet()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'foo';

        $this->assertSame('fooValue', $cache->get($key));
    }

    public function testNegativeGetWithDefaultNull()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'wrong';

        $this->assertNull($cache->get($key));
    }

    public function testNegativeGetWithDefault()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'wrong';

        $default = function () {
            return 'bar';
        };

        $this->assertSame('bar', $cache->get($key, $default));
    }

    public function testRetrieveWithPositiveRetrieve()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'foo';

        $callable = function () {
            return 'bar';
        };

        $this->assertSame('fooValue', $cache->retrieve($key, $callable));
    }

    public function testRetrieveWithNegativeRetrieve()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'wrong';

        $callable = function () {
            return 'bar';
        };

        $this->assertSame('bar', $cache->retrieve($key, $callable));
    }

    public function testMultipleWithoutDefaults()
    {
        $cache = new Cache($this->cacheItemPool);

        $keys = [
            'foo',
            'bar',
        ];

        $result = $cache->multiple($keys);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('foo', $result);
        $this->assertArrayHasKey('bar', $result);
    }

    public function testMultipleWithDefaults()
    {
        $cache = new Cache($this->cacheItemPool);

        $keys = [
            'foo',
            'bar',
            'default',
        ];

        $defaults = [
            'default' => 'defaultValue',
        ];

        $expected = [
            'foo' => 'fooValue',
            'bar' => 'barValue',
            'default' => 'defaultValue',
        ];

        $this->assertSame($expected, $cache->multiple($keys, $defaults));
    }

    public function testPull()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'foo';

        $this->assertSame('fooValue', $cache->pull($key));
    }

    public function testPut()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'key';

        $value = 'value';

        $ttl = 2;

        $this->assertTrue($cache->put($key, $value, $ttl));
    }

    public function testPutMultiple()
    {
        $cache = new Cache($this->cacheItemPool);

        $values = [
            'foo' => 'value1',
            'bar' => 'value2',
        ];

        $ttl = [
            'foo' => 1,
            'bar' => 2,
        ];

        $this->assertNull($cache->putMultiple($values, $ttl));
    }

    public function testPositiveDelete()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'foo';

        $this->assertTrue($cache->delete($key));
    }

    public function testNegativeDelete()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'wrong';

        $this->assertFalse($cache->delete($key));
    }

    public function testCommit()
    {
        $cache = new Cache($this->cacheItemPool);

        $this->assertTrue($cache->commit());
    }

    public function testPrune()
    {
        $cacheItemPool = $this->createMock([CacheItemPoolInterface::class, PruneableInterface::class]);
        $cacheItemPool->method('prune')->willReturn(null);

        $cache = new Cache($cacheItemPool);

        $this->assertNull($cache->prune());
    }

    public function testDeleteMultiple()
    {
        $cache = new Cache($this->cacheItemPool);

        $keys = [
            'foo',
            'bar',
        ];

        $this->assertNull($cache->deleteMultiple($keys));
    }

    public function testClear()
    {
        $cache = new Cache($this->cacheItemPool);

        $this->assertTrue($cache->clear());
    }

    public function testPutDeferred()
    {
        $cache = new Cache($this->cacheItemPool);

        $key = 'foo';

        $value = 'bar';

        $ttl = 2;

        $this->assertTrue($cache->putDeferred($key, $value, $ttl));
    }

    public function testPutMultipleDeferred()
    {
        $cache = new Cache($this->cacheItemPool);

        $values = [
            'foo' => 'fooValue',
            'bar' => 'barValue',
        ];

        $ttl = [
            'foo' => 2,
        ];

        $this->assertNull($cache->putMultipleDeferred($values, $ttl));
    }
}
