<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Song;

use function DI\autowire;

return [
    SongDeleterInterface::class => autowire(SongDeleter::class),
];
