<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Disc;

use function DI\autowire;

return [
    DiscLengthUpdaterInterface::class => autowire(DiscLengthUpdater::class),
];
