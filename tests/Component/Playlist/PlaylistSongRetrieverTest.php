<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class PlaylistSongRetrieverTest extends MockeryTestCase
{
    private MockInterface $songRepository;

    private PlaylistSongRetriever $subject;

    public function setUp(): void
    {
        $this->songRepository = Mockery::mock(SongRepositoryInterface::class);

        $this->subject = new PlaylistSongRetriever(
            $this->songRepository,
        );
    }

    public function testRetrieveYieldsSongs(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);
        $song = Mockery::mock(SongInterface::class);

        $songId = 666;
        $missingSongId = 42;

        $playlist->shouldReceive('getSongList')
            ->withNoArgs()
            ->once()
            ->andReturn([$songId, $missingSongId]);

        $this->songRepository->shouldReceive('find')
            ->with($songId)
            ->once()
            ->andReturn($song);
        $this->songRepository->shouldReceive('find')
            ->with($missingSongId)
            ->once()
            ->andReturnNull();

        $this->assertSame(
            [$song],
            iterator_to_array($this->subject->retrieve($playlist))
        );
    }
}
