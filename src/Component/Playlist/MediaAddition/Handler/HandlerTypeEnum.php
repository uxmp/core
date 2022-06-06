<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition\Handler;

enum HandlerTypeEnum: string
{
    case SONG = 'song';
    case ALBUM = 'album';
    case ARTIST = 'artist';
}
