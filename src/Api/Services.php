<?php

declare(strict_types=1);

namespace Usox\Core\Api;

use Usox\Core\Api\Album\AlbumListApplication;
use Usox\Core\Api\Artist\ArtistListApplication;
use Usox\Core\Api\Playback\PlaySongApplication;
use function DI\autowire;

return [
    AlbumListApplication::class => autowire(),
    ArtistListApplication::class => autowire(),
    PlaySongApplication::class => autowire(),
];
