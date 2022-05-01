<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Song;

use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

final class SongDeleter implements SongDeleterInterface
{
    public function __construct(
        private readonly SongRepositoryInterface $songRepository,
        private readonly PlaybackHistoryRepositoryInterface $playbackHistoryRepository,
    ) {
    }

    public function delete(SongInterface $song): void
    {
        $result = $this->playbackHistoryRepository->findBySong($song);

        foreach ($result as $item) {
            $this->playbackHistoryRepository->delete($item);
        }

        $this->songRepository->delete($song);
    }
}
