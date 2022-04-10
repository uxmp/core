<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition\Handler;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class SongHandlerTest extends MockeryTestCase
{
    private MockInterface $songRepository;

    private SongHandler $subject;

    public function setUp(): void
    {
        $this->songRepository = Mockery::mock(SongRepositoryInterface::class);

        $this->subject = new SongHandler(
            $this->songRepository,
        );
    }

    public function testHandleAddSongId(): void
    {
        $mediaId = 666;
        $songList = [];

        $song = Mockery::mock(SongInterface::class);

        $this->songRepository->shouldReceive('find')
            ->with($mediaId)
            ->once()
            ->andReturn($song);

        $song->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($mediaId);

        $this->subject->handle($mediaId, $songList);

        $this->assertSame(
            [$mediaId],
            $songList
        );
    }
}
