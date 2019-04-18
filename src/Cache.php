<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache;

use Psr\Cache\CacheItemPoolInterface;

final class Cache implements CacheInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * Cache constructor.
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function pool(): CacheItemPoolInterface
    {
        return $this->cacheItemPool;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @throws \Psr\Cache\InvalidArgumentException
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (!$this->has($key)) {
            if (\is_object($default) && ($default instanceof \Closure)) {
                return $default();
            }

            return $default;
        }

        return $this->cacheItemPool->getItem($key)->get();
    }

    /**
     * @param string $key
     * @param callable $retrieveCallable
     * @param int|null $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     * @return mixed
     */
    public function retrieve(string $key, callable $retrieveCallable, int $ttl = null)
    {
        if (!$this->has($key)) {
            $result = $retrieveCallable();

            $this->put($key, $result, $ttl);

            return $result;
        }

        return $this->get($key);
    }

    /**
     * @param array $keys
     * @param array $defaults
     * @throws \Psr\Cache\InvalidArgumentException
     * @return array
     */
    public function multiple(array $keys, array $defaults = []): array
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, (\array_key_exists($key, $defaults)) ? $defaults[$key] : null);
        }

        return $result;
    }

    /**
     * @param string $key
     * @throws \Psr\Cache\InvalidArgumentException
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->cacheItemPool->hasItem($key);
    }

    /**
     * @param string $key
     * @param null $default
     * @throws \Psr\Cache\InvalidArgumentException
     * @return mixed
     */
    public function pull(string $key, $default = null)
    {
        $result = $this->get($key, $default);
        $this->delete($key);

        return $result;
    }

    /**
     * @param string $key
     * @param $value
     * @param int|null $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function put(string $key, $value, int $ttl = null): void
    {
        $item = $this->cacheItemPool->getItem($key);
        $item->set($value);
        $item->expiresAfter($ttl);
        $this->cacheItemPool->save($item);
    }

    /**
     * @param array $values
     * @param array $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function putMultiple(array $values, array $ttl = []): void
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, (\array_key_exists($key, $ttl)) ? $ttl[$key] : null);
        }
    }

    /**
     * @param string $key
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function delete(string $key): void
    {
        $this->cacheItemPool->deleteItem($key);
    }

    /**
     * @param array $keys
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteMultiple(array $keys): void
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    /**
     *
     */
    public function clear(): void
    {
        $this->cacheItemPool->clear();
    }

    /**
     *
     */
    public function prune(): void
    {
        if ($this->cacheItemPool instanceof PruneableInterface) {
            $this->cacheItemPool->prune();
            return;
        }
    }

    /**
     * @param string $key
     * @param $value
     * @param int|null $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function putDeferred(string $key, $value, int $ttl = null): void
    {
        $item = $this->cacheItemPool->getItem($key);
        $item->set($value);
        $item->expiresAfter($ttl);
        $this->cacheItemPool->saveDeferred($item);
    }

    /**
     * @param array $values
     * @param array $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function putMultipleDeferred(array $values, array $ttl = []): void
    {
        foreach ($values as $key => $value) {
            $this->putDeferred($key, $value, (\array_key_exists($key, $ttl)) ? $ttl[$key] : null);
        }
    }

    /**
     *
     */
    public function commit(): void
    {
        $this->cacheItemPool->commit();
    }
}
