<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache;

use Ixocreate\Contract\Cache\PruneableInterface;
use Ixocreate\Contract\Cache\ResetableInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\ResettableInterface;
use Symfony\Contracts\Service\ResetInterface;

final class CacheItemPool implements CacheItemPoolInterface, PruneableInterface, ResetableInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * CachePool constructor.
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function innerPool(): CacheItemPoolInterface
    {
        return $this->cacheItemPool;
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item
     *
     * @throws invalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown
     *
     * @return cacheItemInterface
     *   The corresponding Cache Item
     */
    public function getItem($key)
    {
        return $this->cacheItemPool->getItem($key);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *   An indexed array of keys of items to retrieve
     *
     * @throws invalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown
     *
     * @return array|\Traversable
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function getItems(array $keys = array())
    {
        return $this->cacheItemPool->getItems($keys);
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *   The key for which to check existence
     *
     * @throws invalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown
     *
     * @return bool
     *   True if item exists in the cache, false otherwise
     */
    public function hasItem($key)
    {
        return $this->cacheItemPool->hasItem($key);
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        return $this->cacheItemPool->clear();
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key to delete
     *
     * @throws invalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        return $this->cacheItemPool->deleteItem($key);
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys
     *   An array of keys that should be removed from the pool
     * @throws invalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys)
    {
        return $this->cacheItemPool->deleteItems($keys);
    }

    /**
     * Persists a cache item immediately.
     *
     * @param cacheItemInterface $item
     *   The cache item to save
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item)
    {
        return $this->cacheItemPool->save($item);
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param cacheItemInterface $item
     *   The cache item to save
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->cacheItemPool->saveDeferred($item);
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        return $this->cacheItemPool->commit();
    }

    /**
     *
     */
    public function prune(): void
    {
        if ($this->cacheItemPool instanceof \Symfony\Component\Cache\PruneableInterface) {
            $this->cacheItemPool->prune();
        } elseif ($this->cacheItemPool instanceof PruneableInterface) {
            $this->cacheItemPool->prune();
        }
    }

    /**
     *
     */
    public function reset(): void
    {
        if ($this->cacheItemPool instanceof ResetableInterface) {
            $this->cacheItemPool->reset();
        } elseif ($this->cacheItemPool instanceof ResettableInterface) {
            $this->cacheItemPool->reset();
        } elseif ($this->cacheItemPool instanceof ResetInterface) {
            $this->cacheItemPool->reset();
        }
    }
}
