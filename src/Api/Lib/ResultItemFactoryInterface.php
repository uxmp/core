<?php

namespace Uxmp\Core\Api\Lib;

use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\SongInterface;

interface ResultItemFactoryInterface
{
    public function createSongListItem(
        SongInterface $song,
        AlbumInterface $album
    ): SongListItemInterface;
}
