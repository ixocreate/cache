<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache;

interface CacheableInterface
{
    /**
     * @return mixed
     */
    public function uncachedResult();

    /**
     * @return string
     */
    public function cacheName(): string;

    /**
     * @return string
     */
    public function cacheKey(): string;

    /**
     * @return int
     */
    public function cacheTtl(): int;
}
