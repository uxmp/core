<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist;

use Uxmp\Core\Component\Playlist\MediaAddition\Handler\HandlerTypeEnum;
use Uxmp\Core\Component\Playlist\MediaAddition\PlaylistMediaAdder;
use Uxmp\Core\Component\Playlist\MediaAddition\PlaylistMediaAdderInterface;
use function DI\autowire;
use function DI\get;

return [
    MediaAddition\Handler\ArtistHandler::class => autowire(),
    MediaAddition\Handler\AlbumHandler::class => autowire(),
    MediaAddition\Handler\SongHandler::class => autowire(),
    PlaylistMediaAdderInterface::class => autowire(PlaylistMediaAdder::class)
        ->constructorParameter(
            'handlerList',
            [
                HandlerTypeEnum::ARTIST => get(MediaAddition\Handler\ArtistHandler::class),
                HandlerTypeEnum::ALBUM => get(MediaAddition\Handler\AlbumHandler::class),
                HandlerTypeEnum::SONG => get(MediaAddition\Handler\SongHandler::class),
            ]
        ),
    PlaylistSongRetrieverInterface::class => autowire(PlaylistSongRetriever::class),
];
