<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag\Container;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class AudioFileTest extends MockeryTestCase
{
    private AudioFile $subject;

    public function setUp(): void
    {
        $this->subject = new AudioFile();
    }

    /**
     * @dataProvider setterGetterDataProvider
     */
    public function testGetterSetters(
        string $method,
        mixed $value
    ): void {
        $this->subject->{'set'.$method}($value);

        $this->assertSame(
            $value,
            $this->subject->{'get'.$method}()
        );
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['TrackNumber', 666],
            ['Filename', 'some-filename'],
            ['Title', 'some-title'],
            ['Mbid', 'some-mbid'],
            ['ArtistTitle', 'some-artist-title'],
            ['ArtistMbid', 'some-artist-mbid'],
            ['AlbumTitle', 'some-album-title'],
            ['AlbumMbid', 'some-album-mbid'],
            ['DiscMbid', 'some-disc-mbid'],
            ['DiscNumber', 666],
        ];
    }

    public function testIsValidReturnsFalseIfNotValid(): void
    {
        $this->assertFalse(
            $this->subject->isValid()
        );
    }

    public function testIsValidReturnsTrueIfValuesHaveBeenSet(): void
    {
        $this->subject
            ->setMbid('some-mbid')
            ->setArtistMbid('some-artist-mbid')
            ->setAlbumMbid('some-album-mbid')
        ;

        $this->assertTrue(
            $this->subject->isValid()
        );
    }
}
