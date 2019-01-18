<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache;

use Ixocreate\Contract\Cache\CacheableInterface;
use Psr\Container\ContainerInterface;

final class CacheManager
{
    /**
     * @var ContainerInterface
     */
    private $cacheContainer;

    /**
     * CacheManager constructor.
     * @param ContainerInterface $cacheContainer
     */
    public function __construct(ContainerInterface $cacheContainer)
    {
        $this->cacheContainer = $cacheContainer;
    }

    /**
     * @param CacheableInterface $cacheable
     * @throws \Psr\Cache\InvalidArgumentException
     * @return mixed
     */
    public function fetch(CacheableInterface $cacheable)
    {
        $cacheItemPool = $this->cacheContainer->get($cacheable->cacheName());

        return (new Cache($cacheItemPool))->retrieve(
            $cacheable->cacheKey(),
            function () use ($cacheable) {
                return $cacheable->uncachedResult();
            },
            $cacheable->cacheTtl()
        );
    }
}
