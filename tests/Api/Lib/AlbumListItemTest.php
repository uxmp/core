<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;

class AlbumListItemTest extends MockeryTestCase
{
    private MockInterface $config;

    private MockInterface $album;

    private AlbumListItem $subject;

    public function setUp(): void
    {
        $this->config = Mockery::mock(ConfigProviderInterface::class);
        $this->album = Mockery::mock(AlbumInterface::class);

        $this->subject = new AlbumListItem(
            $this->config,
            $this->album,
        );
    }

    public function testJsonSerializeReturnsData(): void
    {
        $album = Mockery::mock(AlbumInterface::class);
        $artist = Mockery::mock(ArtistInterface::class);

        $albumId = 666;
        $artistId = 42;
        $albumName = 'some-album-name';
        $artistName = 'some-artist-name';
        $baseUrl = 'some-base-url';
        $length = 33;

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $this->album->shouldReceive('getArtist')
            ->withNoArgs()
            ->once()
            ->andReturn($artist);
        $this->album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);
        $this->album->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($albumName);
        $this->album->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($length);

        $artist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($artistId);
        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($artistName);

        $this->assertSame(
            $this->subject->jsonSerialize(),
            [
                'id' => $albumId,
                'artistId' => $artistId,
                'artistName' => $artistName,
                'name' => $albumName,
                'cover' => sprintf($baseUrl . '/art/album/%d', $albumId),
                'length' => $length,
            ]
        );
    }
}
