<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag\Extractor;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Uxmp\Core\Component\Tag\Container\AudioFileInterface;

class Id3v2ExtractorTest extends MockeryTestCase
{
    private string $mbid = 'some-mbid';

    private string $title = 'some-title';

    private string $trackNumber = 'some-number';

    private string $artistTitle = 'some-artist-title';

    private string $artistMbid = 'some-artist-mbid';

    private string $albumMbid = 'some-album-mbid';

    private string $albumTitle = 'some-album-title';

    private string $discMbid = 'some-disc-mbid';

    private string $discNumber = '1';

    private string $year = '6666';

    private array $genres = ['meddal'];

    private Id3v2Extractor $subject;

    public function setUp(): void
    {
        $this->subject = new Id3v2Extractor();
    }

    public function testAppliesReturnsFalseIfNotApplies(): void
    {
        $this->assertFalse(
            $this->subject->applies(['id3v1' => []])
        );
    }

    public function testAppliesReturnsTrueIfApplies(): void
    {
        $this->assertTrue(
            $this->subject->applies(['id3v2' => []])
        );
    }

    public function testExtractExtracts(): void
    {
        $data = [
            'text' => [
                'MusicBrainz Release Track Id' => $this->mbid,
                'MusicBrainz Album Artist Id' => $this->artistMbid,
                'MusicBrainz Album Id' => $this->albumMbid,
                'MusicBrainz Release Group Id' => $this->discMbid,
                'originalyear' => $this->year,
            ],
            'title' => [$this->title],
            'track_number' => [$this->trackNumber],
            'artist' => [$this->artistTitle],
            'album' => [$this->albumTitle],
            'part_of_a_set' => [$this->discNumber . '/1'],
            'genre' => $this->genres,
        ];

        $audioFile = Mockery::mock(AudioFileInterface::class);

        $audioFile->shouldReceive('setMbid')
            ->with($this->mbid)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setTitle')
            ->with($this->title)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setTrackNumber')
            ->with((int) $this->trackNumber)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setArtistTitle')
            ->with($this->artistTitle)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setArtistMbid')
            ->with($this->artistMbid)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setAlbumTitle')
            ->with($this->albumTitle)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setAlbumMbid')
            ->with($this->albumMbid)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setDiscMbid')
            ->with($this->discMbid)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setDiscNumber')
            ->with((int) $this->discNumber)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setYear')
            ->with((int) $this->year)
            ->once()
            ->andReturnSelf();
        $audioFile->shouldReceive('setGenres')
            ->with($this->genres)
            ->once()
            ->andReturnSelf();

        $this->subject->extract(['id3v2' => $data], $audioFile);
    }
}
