<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition\Handler;

use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

/**
 * Adds songs of an album to the list of songs
 */
final class AlbumHandler implements HandlerInterface
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository,
    ) {
    }

    /**
     * Adds all eligible song ids of the album to the songList array
     *
     * @param array<int> $songList
     */
    public function handle(int $mediaId, array &$songList): void
    {
        $album = $this->albumRepository->find($mediaId);
        if ($album !== null) {
            foreach ($album->getDiscs() as $disc) {
                foreach ($disc->getSongs() as $song) {
                    $songList[] = $song->getId();
                }
            }
        }
    }
}
