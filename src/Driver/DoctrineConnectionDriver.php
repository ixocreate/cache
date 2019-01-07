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
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Doctrine\DBAL\Connection;

final class DoctrineConnectionDriver implements DriverInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $defaultLifetime;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(string $name, array $options)
    {
        $this->name = $name;

        $this->defaultLifetime = (isset($options['defaultLifetime']) && \is_int($options['defaultLifetime'])) ? $options['defaultLifetime'] : 0;

        if (isset($options['connection']) && $options['connection'] instanceof Connection) {
            $this->connection = $options['connection'];
        }
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
            new PdoAdapter(
                $this->connection(),
                $this->name(),
                $this->defaultLifetime(),
                [
                    'db_table' => 'cache_item',
                    'db_id_col' => 'id',
                    'db_data_col' => 'item',
                    'db_lifetime_col' => 'lifetime',
                    'db_time_col' => 'createdAt',
                ]
            )
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
     * @return Connection
     */
    public function connection(): Connection
    {
        return $this->connection;
    }
}
