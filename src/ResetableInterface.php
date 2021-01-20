<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Cache;

use Symfony\Component\Cache\ResettableInterface;

interface ResetableInterface extends ResettableInterface
{
    public function reset(): void;
}
