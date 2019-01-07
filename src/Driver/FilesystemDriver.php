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
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class FilesystemDriver implements DriverInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var int
     */
    private $defaultLifetime;

    public function __construct(string $name, array $options)
    {
        $this->name = $name;

        $this->directory = (isset($options['directory']) && \is_string($options['directory'])) ? $options['directory'] : \sys_get_temp_dir();
        $this->defaultLifetime = (isset($options['defaultLifetime']) && \is_int($options['defaultLifetime'])) ? $options['defaultLifetime'] : 0;
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
            new FilesystemAdapter($this->name(), $this->defaultLifetime(), $this->directory())
        );
    }

    /**
     * @return string
     */
    public function directory(): string
    {
        return $this->directory;
    }

    /**
     * @return int
     */
    public function defaultLifetime(): int
    {
        return $this->defaultLifetime;
    }
}
