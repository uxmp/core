<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist;

use Generator;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;

/**
 * Retrieve all song objects of a playlist
 */
interface PlaylistSongRetrieverInterface
{
    /**
     * Yields every song from the playlist, ignores missing songs
     *
     * @return Generator<SongInterface>
     */
    public function retrieve(PlaylistInterface $playlist): Generator;
}
