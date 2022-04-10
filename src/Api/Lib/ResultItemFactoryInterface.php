<?php

namespace Uxmp\Core\Api\Lib;

use JsonSerializable;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;

interface ResultItemFactoryInterface
{
    public function createSongListItem(
        SongInterface $song,
        AlbumInterface $album
    ): JsonSerializable;

    public function createPlaybackHistoryItem(
        PlaybackHistoryInterface $playbackHistory
    ): JsonSerializable;

    public function createPlaylistItem(
        PlaylistInterface $playlist
    ): JsonSerializable;
}
