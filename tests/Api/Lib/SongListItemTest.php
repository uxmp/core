<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Model\SongInterface;

class SongListItemTest extends MockeryTestCase
{
    private MockInterface $config;

    private MockInterface $song;

    private MockInterface $album;

    private SongListItem $subject;

    public function setUp(): void
    {
        $this->config = Mockery::mock(ConfigProviderInterface::class);
        $this->song = Mockery::mock(SongInterface::class);
        $this->album = Mockery::mock(AlbumInterface::class);

        $this->subject = new SongListItem(
            $this->config,
            $this->song,
            $this->album
        );
    }

    public function testJsonSerializeReturnsData(): void
    {
        $artist = Mockery::mock(ArtistInterface::class);

        $albumId = 666;
        $songId = 42;
        $songTitle = 'some-song-title';
        $artistTitle = 'some-artist-title';
        $albumTitle = 'some-album-title';
        $trackNumber = 33;
        $artistId = 21;
        $baseUrl = 'some-base-url';
        $cover = sprintf('%s/art/album/%d', $baseUrl, $albumId);
        $length = 123;
        $year = 11;

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $this->album->shouldReceive('getArtist')
            ->withNoArgs()
            ->once()
            ->andReturn($artist);

        $this->song->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($songId);
        $this->song->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($songTitle);
        $this->song->shouldReceive('getTrackNumber')
            ->withNoArgs()
            ->once()
            ->andReturn($trackNumber);
        $this->song->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($length);
        $this->song->shouldReceive('getYear')
            ->withNoArgs()
            ->once()
            ->andReturn($year);

        $this->album->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($albumTitle);
        $this->album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);

        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($artistTitle);
        $artist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($artistId);

        $this->assertSame(
            [
                'id' => $songId,
                'name' => $songTitle,
                'artistName' => $artistTitle,
                'albumName' => $albumTitle,
                'trackNumber' => $trackNumber,
                'playUrl' => sprintf('%s/play/%d', $baseUrl, $songId),
                'cover' => $cover,
                'artistId' => $artistId,
                'albumId' => $albumId,
                'length' => $length,
                'year' => $year,
            ],
            $this->subject->jsonSerialize()
        );
    }
}
