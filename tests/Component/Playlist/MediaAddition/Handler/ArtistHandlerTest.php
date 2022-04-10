<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition\Handler;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class ArtistHandlerTest extends MockeryTestCase
{
    private MockInterface $artistRepository;

    private ArtistHandler $subject;

    public function setUp(): void
    {
        $this->artistRepository = Mockery::mock(ArtistRepositoryInterface::class);

        $this->subject = new ArtistHandler(
            $this->artistRepository,
        );
    }

    public function testHandleAddsSongs(): void
    {
        $mediaId = 666;
        $songId = 42;
        $songList = [];

        $artist = Mockery::mock(ArtistInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $disc = Mockery::mock(DiscInterface::class);
        $song = Mockery::mock(SongInterface::class);

        $this->artistRepository->shouldReceive('find')
            ->with($mediaId)
            ->once()
            ->andReturn($artist);

        $artist->shouldReceive('getAlbums')
            ->withNoArgs()
            ->once()
            ->andReturn([$album]);

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
            $songList
        );
    }
}
