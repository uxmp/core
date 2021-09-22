<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Artist;

use function DI\autowire;

return [
    ArtistCoverUpdaterInteface::class => autowire(ArtistCoverUpdater::class),
];
