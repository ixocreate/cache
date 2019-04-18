<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache;

use Psr\Container\ContainerInterface;

final class CacheManager
{
    /**
     * @var ContainerInterface
     */
    private $cacheContainer;

    /**
     * CacheManager constructor.
     *
     * @param ContainerInterface $cacheContainer
     */
    public function __construct(ContainerInterface $cacheContainer)
    {
        $this->cacheContainer = $cacheContainer;
    }

    /**
     * @param CacheableInterface $cacheable
     * @param bool $force
     * @throws \Psr\Cache\InvalidArgumentException
     * @return mixed
     */
    public function fetch(CacheableInterface $cacheable, bool $force = false)
    {
        $cacheItemPool = $this->cacheContainer->get($cacheable->cacheName());

        if ($force === true) {
            $result = $cacheable->uncachedResult();
            (new Cache($cacheItemPool))->put(
                $cacheable->cacheKey(),
                $result,
                $cacheable->cacheTtl()
            );
            return $result;
        }

        return (new Cache($cacheItemPool))->retrieve(
            $cacheable->cacheKey(),
            function () use ($cacheable) {
                return $cacheable->uncachedResult();
            },
            $cacheable->cacheTtl()
        );
    }
}
