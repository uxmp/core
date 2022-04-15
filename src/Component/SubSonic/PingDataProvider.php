<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Usox\HyperSonic\FeatureSet\V1161\Contract\PingDataProviderInterface;

/**
 * Returns the state of the server
 *
 * @todo maybe we can check for some sort of `maintenance mode` at a later time
 */
final class PingDataProvider implements PingDataProviderInterface
{
    public function isOk(): bool
    {
        return true;
    }
}
