<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Usox\HyperSonic\FeatureSet\V1161\Contract\PingDataProviderInterface;

final class PingDataProvider implements PingDataProviderInterface
{
    public function isOk(): bool
    {
        return true;
    }
}
