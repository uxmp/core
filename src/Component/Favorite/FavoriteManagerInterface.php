<?php

namespace Uxmp\Core\Component\Favorite;

use Uxmp\Core\Orm\Model\UserInterface;

interface FavoriteManagerInterface
{
    public function add(
        FavoriteAbleInterface $obj,
        UserInterface $user,
    ): bool;

    public function remove(
        FavoriteAbleInterface $obj,
        UserInterface $user,
    ): bool;
}
