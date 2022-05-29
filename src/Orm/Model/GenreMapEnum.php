<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

enum GenreMapEnum: string
{
    case ARTIST = 'artist';
    case ALBUM = 'album';
    case SONG = 'song';
}
