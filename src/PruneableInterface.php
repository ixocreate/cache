<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache;

interface PruneableInterface extends \Symfony\Component\Cache\PruneableInterface
{
    public function prune(): void;
}
