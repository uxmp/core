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
                HandlerTypeEnum::ARTIST->value => get(MediaAddition\Handler\ArtistHandler::class),
                HandlerTypeEnum::ALBUM->value => get(MediaAddition\Handler\AlbumHandler::class),
                HandlerTypeEnum::SONG->value => get(MediaAddition\Handler\SongHandler::class),
            ]
        ),
    'playlistTypeHandler' => [
        PlaylistTypeEnum::STATIC->value => get(Smartlist\Type\StaticPlaylistType::class),
        PlaylistTypeEnum::FAVORITES->value => get(Smartlist\Type\FavoriteSongsType::class),
    ],
    PlaylistSongRetrieverInterface::class => autowire(PlaylistSongRetriever::class)
        ->constructorParameter(
            'handlerTypes',
            get('playlistTypeHandler')
        ),
    Smartlist\Type\FavoriteSongsType::class => autowire(),
    Smartlist\Type\StaticPlaylistType::class => autowire(),
];
