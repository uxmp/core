<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist;

use Generator;
use Uxmp\Core\Component\Playlist\Exception\InvalidPlaylistTypeException;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\UserInterface;

/**
 * Retrieve all song objects of a playlist
 */
interface PlaylistSongRetrieverInterface
{
    /**
     * Yields every song from the playlist, ignores missing songs
     *
     * @return Generator<SongInterface>
     *
     * @throws InvalidPlaylistTypeException
     */
    public function retrieve(
        PlaylistInterface $playlist,
        UserInterface $user,
    ): Generator;
}
