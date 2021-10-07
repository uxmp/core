<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use function DI\autowire;

return [
    CachedArtResponseProviderInterface::class => autowire(CachedArtResponseProvider::class),
];
