<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache;

use Psr\Cache\CacheItemPoolInterface;

interface CacheInterface
{
    /**
     * @return CacheItemPoolInterface
     */
    public function pool(): CacheItemPoolInterface;

    /**
     * @param string $key
     * @param mixed $default
     * @throws \Psr\Cache\InvalidArgumentException
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     * @param callable $retrieveCallable
     * @param int|null $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     * @return mixed
     */
    public function retrieve(string $key, callable $retrieveCallable, int $ttl = null);

    /**
     * @param array $keys
     * @param array $defaults
     * @throws \Psr\Cache\InvalidArgumentException
     * @return array
     */
    public function multiple(array $keys, array $defaults = []): array;

    /**
     * @param string $key
     * @throws \Psr\Cache\InvalidArgumentException
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @param string $key
     * @param null $default
     * @throws \Psr\Cache\InvalidArgumentException
     * @return mixed
     */
    public function pull(string $key, $default = null);

    /**
     * @param string $key
     * @param $value
     * @param int|null $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function put(string $key, $value, int $ttl = null): void;

    /**
     * @param array $values
     * @param array $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function putMultiple(array $values, array $ttl = []): void;

    /**
     * @param string $key
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function delete(string $key): void;

    /**
     * @param array $keys
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteMultiple(array $keys): void;

    /**
     *
     */
    public function clear(): void;

    /**
     *
     */
    public function prune(): void;

    /**
     * @param string $key
     * @param $value
     * @param int|null $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function putDeferred(string $key, $value, int $ttl = null): void;

    /**
     * @param array $values
     * @param array $ttl
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function putMultipleDeferred(array $values, array $ttl = []): void;

    /**
     *
     */
    public function commit(): void;
}
