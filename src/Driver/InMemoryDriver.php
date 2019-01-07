<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache\Driver;

use Ixocreate\Cache\CacheItemPool;
use Ixocreate\Contract\Cache\DriverInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class InMemoryDriver implements DriverInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $storeSerialized;

    /**
     * @var int
     */
    private $defaultLifetime;

    public function __construct(string $name, array $options)
    {
        $this->name = $name;

        $this->defaultLifetime = (isset($options['defaultLifetime']) && \is_int($options['defaultLifetime'])) ? $options['defaultLifetime'] : 0;
        $this->storeSerialized = (\array_key_exists('storeSerialized', $options) && \is_bool($options['storeSerialized'])) ? $options['storeSerialized'] : false;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function create(): CacheItemPoolInterface
    {
        return new CacheItemPool(
            new ArrayAdapter($this->defaultLifetime(), $this->storeSerialized())
        );
    }

    /**
     * @return int
     */
    public function defaultLifetime(): int
    {
        return $this->defaultLifetime;
    }

    /**
     * @return bool
     */
    public function storeSerialized(): bool
    {
        return $this->storeSerialized;
    }
}
