<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist;

use Generator;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

/**
 * Retrieve all song objects of a playlist
 */
final class PlaylistSongRetriever implements PlaylistSongRetrieverInterface
{
    public function __construct(
        private SongRepositoryInterface $songRepository,
    ) {
    }

    /**
     * Yields every song from the playlist, ignores missing songs
     *
     * @return Generator<SongInterface>
     */
    public function retrieve(PlaylistInterface $playlist): Generator
    {
        $songList = $playlist->getSongList();

        foreach ($songList as $songId) {
            $song = $this->songRepository->find($songId);
            if ($song !== null) {
                yield $song;
            }
        }
    }
}
