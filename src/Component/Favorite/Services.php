<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Favorite;

use function DI\autowire;

return [
    FavoriteManagerInterface::class => autowire(FavoriteManager::class),
];
