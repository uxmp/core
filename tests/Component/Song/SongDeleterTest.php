<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Song;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class SongDeleterTest extends MockeryTestCase
{
    private MockInterface $songRepository;

    private MockInterface $playbackHistoryRepository;

    private SongDeleter $subject;

    public function setUp(): void
    {
        $this->songRepository = \Mockery::mock(SongRepositoryInterface::class);
        $this->playbackHistoryRepository = \Mockery::mock(PlaybackHistoryRepositoryInterface::class);

        $this->subject = new SongDeleter(
            $this->songRepository,
            $this->playbackHistoryRepository,
        );
    }

    public function testDeleteDeletes(): void
    {
        $song = \Mockery::mock(SongInterface::class);
        $historyItem = \Mockery::mock(PlaybackHistoryInterface::class);

        $this->songRepository->shouldReceive('delete')
            ->with($song)
            ->once();

        $this->playbackHistoryRepository->shouldReceive('findBySong')
            ->with($song)
            ->once()
            ->andReturn([$historyItem]);
        $this->playbackHistoryRepository->shouldReceive('delete')
            ->with($historyItem)
            ->once();

        $this->subject->delete($song);
    }
}
