<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition\Handler;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class AlbumHandlerTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private AlbumHandler $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);

        $this->subject = new AlbumHandler(
            $this->albumRepository,
        );
    }

    public function testHandleAddsSongs(): void
    {
        $mediaId = 666;
        $songId = 42;
        $songList = [];

        $album = Mockery::mock(AlbumInterface::class);
        $disc = Mockery::mock(DiscInterface::class);
        $song = Mockery::mock(SongInterface::class);

        $this->albumRepository->shouldReceive('find')
            ->with($mediaId)
            ->once()
            ->andReturn($album);

        $album->shouldReceive('getDiscs')
            ->withNoArgs()
            ->once()
            ->andReturn([$disc]);

        $disc->shouldReceive('getSongs')
            ->withNoArgs()
            ->once()
            ->andReturn([$song]);

        $song->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($songId);

        $this->subject->handle($mediaId, $songList);

        $this->assertSame(
            [$songId],
            $songList,
        );
    }
}
