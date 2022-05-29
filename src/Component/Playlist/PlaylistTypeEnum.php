<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist;

enum PlaylistTypeEnum: int
{
    case STATIC = 1;
    case FAVORITES = 2;
}
