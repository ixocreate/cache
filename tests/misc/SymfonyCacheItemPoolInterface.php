<?php

declare(strict_types=1);

namespace Ixocreate\Misc\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\PruneableInterface;
use Symfony\Component\Cache\ResettableInterface;

interface SymfonyCacheItemPoolInterface extends CacheItemPoolInterface, ResettableInterface, PruneableInterface
{

}
