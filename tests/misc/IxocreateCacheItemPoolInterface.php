<?php

declare(strict_types=1);

namespace Ixocreate\Misc\Cache;

use Ixocreate\Cache\PruneableInterface;
use Ixocreate\Cache\ResetableInterface;
use Psr\Cache\CacheItemPoolInterface;

interface IxocreateCacheItemPoolInterface extends CacheItemPoolInterface, ResetableInterface, PruneableInterface
{

}
