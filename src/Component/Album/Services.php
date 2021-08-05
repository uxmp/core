<?php

declare(strict_types=1);

namespace Usox\Core\Component\Album;

use function DI\autowire;

return [
    AlbumCoverUpdaterInterface::class => autowire(AlbumCoverUpdater::class),
];
