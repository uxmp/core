<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Song;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class SongDeleterTest extends MockeryTestCase
{
    private MockInterface $songRepository;

    private SongDeleter $subject;

    public function setUp(): void
    {
        $this->songRepository = \Mockery::mock(SongRepositoryInterface::class);

        $this->subject = new SongDeleter(
            $this->songRepository
        );
    }

    public function testDeleteDeletes(): void
    {
        $song = \Mockery::mock(SongInterface::class);

        $this->songRepository->shouldReceive('delete')
            ->with($song)
            ->once();

        $this->subject->delete($song);
    }
}
