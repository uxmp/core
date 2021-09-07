<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Song;

use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

final class SongDeleter implements SongDeleterInterface
{
    public function __construct(
        private SongRepositoryInterface $songRepository,
    ) {
    }

    public function delete(SongInterface $song): void
    {
        $this->songRepository->delete($song);
    }
}
