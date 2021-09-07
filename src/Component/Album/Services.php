<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Album;

use function DI\autowire;

return [
    AlbumCoverUpdaterInterface::class => autowire(AlbumCoverUpdater::class),
    AlbumDeleterInterface::class => autowire(AlbumDeleter::class),
];
