<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag\Extractor;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Uxmp\Core\Component\Tag\Container\AudioFileInterface;

class VorbisExtractorTest extends MockeryTestCase
{
    private VorbisExtractor $subject;

    public function setUp(): void
    {
        $this->subject = new VorbisExtractor();
    }

    public function testAppliesReturnsTrueIfKeyIsAvailable(): void
    {
        $this->assertTrue(
            $this->subject->applies(['vorbiscomment' => []])
        );
    }

    public function testAppliesReturnsFalseIfKeyIsNotAvailable(): void
    {
        $this->assertFalse(
            $this->subject->applies([])
        );
    }

    public function testExtractSetsData(): void
    {
        $audioFile = \Mockery::mock(AudioFileInterface::class);

        $mbid = 'some-mbid';
        $title = 'some-title';
        $trackNumber = 666;
        $artistTitle = 'some-artist-title';
        $artistMbid = 'some-artist-mbid';
        $albumTitle = 'some-album-title';
        $albumMbid = 'some-album-mbid';
        $discMbId = 'some-disc-mbid';
        $discNumber = 42;
        $data = [
            'musicbrainz_trackid' => [$mbid],
            'title' => [$title],
            'tracknumber' => [(string) $trackNumber],
            'albumartist' => [$artistTitle],
            'musicbrainz_albumartistid' => [$artistMbid],
            'album' => [$albumTitle],
            'musicbrainz_albumid' => [$albumMbid],
            'musicbrainz_releasegroupid' => [$discMbId],
            'discnumber' => [(string) $discNumber],
        ];

        $audioFile->shouldReceive('setMbid')
            ->with($mbid)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setTitle')
            ->with($title)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setTrackNumber')
            ->with($trackNumber)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setArtistTitle')
            ->with($artistTitle)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setArtistMbid')
            ->with($artistMbid)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setAlbumTitle')
            ->with($albumTitle)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setAlbumMbid')
            ->with($albumMbid)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setDiscMbid')
            ->with($discMbId)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setDiscNumber')
            ->with($discNumber)
            ->once()
            ->andReturnSelf();

        $this->subject->extract(['vorbiscomment' => $data], $audioFile);
    }
}
