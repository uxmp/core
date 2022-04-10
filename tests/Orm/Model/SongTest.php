<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Mockery;

class SongTest extends ModelTestCase
{
    /** @var mixed|Song */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new Song();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Title', 'some-title'],
            ['Artist', \Mockery::mock(ArtistInterface::class)],
            ['TrackNumber', 666],
            ['Filename', 'some-filename'],
            ['Mbid', 'some-mbid'],
            ['Disc', \Mockery::mock(DiscInterface::class)],
            ['Catalog', \Mockery::mock(CatalogInterface::class)],
            ['Length', 42],
            ['Year', 33],
            ['MimeType', 'some-type'],
        ];
    }

    public function testGetTypeReturnsType(): void
    {
        $this->assertSame(
            'song',
            $this->subject->getType()
        );
    }

    public function testGetAlbumReturnsInstance(): void
    {
        $disc = Mockery::mock(DiscInterface::class);
        $album = Mockery::mock(AlbumInterface::class);

        $disc->shouldReceive('getAlbum')
            ->withNoArgs()
            ->once()
            ->andReturn($album);

        $this->subject->setDisc($disc);

        $this->assertSame(
            $album,
            $this->subject->getAlbum(),
        );
    }
}
