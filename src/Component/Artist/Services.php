<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Artist;

use function DI\autowire;

return [
    ArtistCoverUpdaterInterface::class => autowire(ArtistCoverUpdater::class),
];
