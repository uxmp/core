<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist;

use Generator;
use Uxmp\Core\Component\Playlist\Exception\InvalidPlaylistTypeException;
use Uxmp\Core\Component\Playlist\Smartlist\Type\SmartlistTypeInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

/**
 * Retrieve all song objects of a playlist
 */
final class PlaylistSongRetriever implements PlaylistSongRetrieverInterface
{
    /**
     * @param array<SmartlistTypeInterface> $handlerTypes
     */
    public function __construct(
        private readonly SongRepositoryInterface $songRepository,
        private readonly array $handlerTypes
    ) {
    }

    /**
     * Yields every song from the playlist, ignores missing songs
     *
     * @return Generator<SongInterface>
     *
     * @throws InvalidPlaylistTypeException
     */
    public function retrieve(
        PlaylistInterface $playlist,
        UserInterface $user
    ): Generator {
        $handler = $this->handlerTypes[$playlist->getType()->value] ?? null;
        if ($handler === null) {
            throw new InvalidPlaylistTypeException();
        }

        $songList = $handler->getSongList(
            $playlist,
            $user,
        );

        foreach ($songList as $songId) {
            $song = $this->songRepository->find($songId);
            if ($song !== null) {
                yield $song;
            }
        }
    }
}
