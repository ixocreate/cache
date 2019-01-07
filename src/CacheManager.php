<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache;

use Ixocreate\Contract\Cache\CacheableInterface;
use Ixocreate\Contract\Cache\CachePoolReceiverInterface;

final class CacheManager
{
    /**
     * @var CachePoolReceiverInterface
     */
    private $cachePoolReceiver;

    /**
     * CacheManager constructor.
     * @param CachePoolReceiverInterface $cachePoolReceiver
     */
    public function __construct(CachePoolReceiverInterface $cachePoolReceiver)
    {
        $this->cachePoolReceiver = $cachePoolReceiver;
    }

    /**
     * @param CacheableInterface $cacheable
     * @throws \Psr\Cache\InvalidArgumentException
     * @return mixed
     */
    public function fetch(CacheableInterface $cacheable)
    {
        $cacheItemPool = $this->cachePoolReceiver->get($cacheable->cacheName());
        $cache = new Cache($cacheItemPool);

        return $cache->retrieve(
            $cacheable->cacheKey(),
            function () use ($cacheable) {
                return $cacheable->uncachedResult();
            },
            $cacheable->cacheTtl()
        );
    }
}
