<?php

namespace Uxmp\Core\Component\Favorite;

interface FavoriteManagerInterface
{
    public function add(
        FavoriteAbleInterface $obj,
        int $userId
    ): bool;

    public function remove(
        FavoriteAbleInterface $obj,
        int $userId
    ): bool;
}
