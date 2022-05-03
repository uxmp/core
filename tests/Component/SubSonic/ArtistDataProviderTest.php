<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class ArtistDataProviderTest extends MockeryTestCase
{
    private MockInterface $artistRepository;

    private ArtistDataProvider $subject;

    public function setUp(): void
    {
        $this->artistRepository = Mockery::mock(ArtistRepositoryInterface::class);

        $this->subject = new ArtistDataProvider(
            $this->artistRepository,
        );
    }

    public function testGetArtistReturnsNullIfArtistWasNotFound(): void
    {
        $artistId = '666';

        $this->artistRepository->shouldReceive('find')
            ->with((int) $artistId)
            ->once()
            ->andReturnNull();

        $this->assertNull(
            $this->subject->getArtist($artistId)
        );
    }

    public function testGetArtistReturnsData(): void
    {
        $artistId = 666;
        $artistName = 'some-name';
        $songCount = 42;
        $duration = 123;
        $albumId = 342;
        $albumName = 'some-album-name';
        $albumCount = 333;

        $album = Mockery::mock(AlbumInterface::class);
        $artist = Mockery::mock(ArtistInterface::class);

        $this->artistRepository->shouldReceive('find')
            ->with($artistId)
            ->once()
            ->andReturn($artist);

        $artist->shouldReceive('getAlbums')
            ->withNoArgs()
            ->once()
            ->andReturn([$album]);
        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($artistName);
        $artist->shouldReceive('getAlbumCount')
            ->withNoArgs()
            ->once()
            ->andReturn($albumCount);

        $album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);
        $album->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($albumName);
        $album->shouldReceive('getSongCount')
            ->withNoArgs()
            ->once()
            ->andReturn($songCount);
        $album->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($duration);

        $this->assertSame(
            [
                'id' => (string) $artistId,
                'name' => $artistName,
                'coverArtId' => 'artist-'.$artistId,
                'artistImageUrl' => '',
                'albumCount' => $albumCount,
                'albums' => [[
                    'id' => $albumId,
                    'name' => $albumName,
                    'coverArtId' => 'album-'.$albumId,
                    'songCount' => $songCount,
                    'createDate' => null,
                    'duration' => $duration,
                    'artistName' => $artistName,
                    'artistId' => (string) $artistId,
                ]],
            ],
            $this->subject->getArtist((string) $artistId)
        );
    }
}
