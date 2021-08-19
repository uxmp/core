<?php

declare(strict_types=1);

namespace Uxmp\Core\Api;

use Uxmp\Core\Api\Album\AlbumListApplication;
use Uxmp\Core\Api\Art\ArtApplication;
use Uxmp\Core\Api\Artist\ArtistListApplication;
use Uxmp\Core\Api\Playback\PlaySongApplication;
use Uxmp\Core\Api\Common\LoginApplication;
use function DI\autowire;

return [
    AlbumListApplication::class => autowire(),
    ArtistListApplication::class => autowire(),
    PlaySongApplication::class => autowire(),
    ArtApplication::class => autowire(),
    LoginApplication::class => autowire(),
];
