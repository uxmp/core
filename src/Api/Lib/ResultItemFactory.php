<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use JsonSerializable;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;

final class ResultItemFactory implements ResultItemFactoryInterface
{
    public function __construct(
        private readonly ConfigProviderInterface $config
    ) {
    }

    public function createSongListItem(
        SongInterface $song,
        AlbumInterface $album
    ): JsonSerializable {
        return new SongListItem(
            $this->config,
            $song,
            $album,
        );
    }

    public function createPlaybackHistoryItem(
        PlaybackHistoryInterface $playbackHistory
    ): JsonSerializable {
        return new PlaybackHistoryItem(
            $this->config,
            $playbackHistory,
        );
    }

    public function createPlaylistItem(
        PlaylistInterface $playlist
    ): JsonSerializable {
        return new PlaylistItem(
            $playlist
        );
    }
}
