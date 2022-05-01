<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition\Handler;

use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

/**
 * Adds single songs to the songlist
 */
final class SongHandler implements HandlerInterface
{
    public function __construct(
        private readonly SongRepositoryInterface $songRepository,
    ) {
    }

    /**
     * Adds all eligible song ids to the songList array
     *
     * @param array<int> $songList
     */
    public function handle(int $mediaId, array &$songList): void
    {
        $song = $this->songRepository->find($mediaId);
        if ($song !== null) {
            $songList[] = $song->getId();
        }
    }
}
