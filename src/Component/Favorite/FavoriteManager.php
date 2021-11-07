<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Favorite;

final class FavoriteManager implements FavoriteManagerInterface
{
    public function add(
        FavoriteAbleInterface $obj,
        int $userId
    ): bool {
        return false;
    }

    public function remove(
        FavoriteAbleInterface $obj,
        int $userId
    ): bool {
        return false;
    }
}
