<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\UserInterface;

class PlaybackHistoryItemTest extends MockeryTestCase
{
    private MockInterface $playbackHistory;

    private MockInterface $config;

    private PlaybackHistoryItem $subject;

    public function setUp(): void
    {
        $this->config = Mockery::mock(ConfigProviderInterface::class);
        $this->playbackHistory = Mockery::mock(PlaybackHistoryInterface::class);

        $this->subject = new PlaybackHistoryItem(
            $this->config,
            $this->playbackHistory
        );
    }

    public function testJsonSerializeReturnsData(): void
    {
        $artist = Mockery::mock(ArtistInterface::class);
        $song = Mockery::mock(SongInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $album = Mockery::mock(AlbumInterface::class);

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
        $userId = 456;
        $userName = 'some-user-name';

        $user->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($userId);
        $user->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn($userName);

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $song->shouldReceive('getDisc->getAlbum')
            ->withNoArgs()
            ->once()
            ->andReturn($album);
        $song->shouldReceive('getArtist')
            ->withNoArgs()
            ->once()
            ->andReturn($artist);

        $this->playbackHistory->shouldReceive('getSong')
            ->withNoArgs()
            ->once()
            ->andReturn($song);
        $this->playbackHistory->shouldReceive('getUser')
            ->withNoArgs()
            ->once()
            ->andReturn($user);

        $song->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($songId);
        $song->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($songTitle);
        $song->shouldReceive('getTrackNumber')
            ->withNoArgs()
            ->once()
            ->andReturn($trackNumber);
        $song->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($length);

        $album->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($albumTitle);
        $album->shouldReceive('getId')
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
                'userId' => $userId,
                'userName' => $userName,
            ],
            $this->subject->jsonSerialize()
        );
    }
}
