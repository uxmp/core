<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Usox\HyperSonic\FeatureSet\V1161\Contract\ArtistListDataProviderInterface;
use function DI\autowire;

return [
    PingDataProvider::class => autowire(),
    LicenseDataProvider::class => autowire(),
    ArtistListDataProviderInterface::class => autowire(),
    AuthenticationProvider::class => autowire(),
];
