<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition\Handler;

use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

/**
 * Adds songs of an artist to the list of songs
 */
final class ArtistHandler implements HandlerInterface
{
    public function __construct(
        private readonly ArtistRepositoryInterface $artistRepository,
    ) {
    }

    /**
     * Adds all eligible song ids of the artists' album to the songList array
     *
     * @param array<int> $songList
     */
    public function handle(int $mediaId, array &$songList): void
    {
        $artist = $this->artistRepository->find($mediaId);
        if ($artist !== null) {
            foreach ($artist->getAlbums() as $album) {
                foreach ($album->getDiscs() as $disc) {
                    foreach ($disc->getSongs() as $song) {
                        $songList[] = $song->getId();
                    }
                }
            }
        }
    }
}
