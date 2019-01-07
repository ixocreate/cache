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

final class PdoDriver implements DriverInterface
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
     * @var
     */
    private $connection;

    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $username = '';

    /**
     * @var string
     */
    private $password = '';

    public function __construct(string $name, array $options)
    {
        $this->name = $name;

        $this->defaultLifetime = (isset($options['defaultLifetime']) && \is_int($options['defaultLifetime'])) ? $options['defaultLifetime'] : 0;

        if (isset($options['connection']) && $options['connection'] instanceof \PDO) {
            $this->connection = $options['connection'];
        }

        if (isset($options['dsn']) && \is_string($options['dsn'])) {
            $this->dsn = $options['dsn'];
        }

        if (isset($options['username']) && \is_string($options['username'])) {
            $this->username = $options['username'];
        }

        if (isset($options['password']) && \is_string($options['password'])) {
            $this->password = $options['password'];
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
                ($this->connection() instanceof \PDO) ? $this->connection() : $this->dsn(),
                $this->name(),
                $this->defaultLifetime(),
                [
                    'db_table' => 'cache_item',
                    'db_id_col' => 'id',
                    'db_data_col' => 'item',
                    'db_lifetime_col' => 'lifetime',
                    'db_time_col' => 'createdAt',
                    'db_username' => $this->username(),
                    'db_password' => $this->password(),
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
     * @return \PDO|null
     */
    public function connection(): ?\PDO
    {
        return $this->connection;
    }

    /**
     * @return string|null
     */
    public function dsn(): ?string
    {
        return $this->dsn;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->password;
    }
}
