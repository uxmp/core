<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

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
        ];
    }

    public function testGetTypeReturnsType(): void
    {
        $this->assertSame(
            'song',
            $this->subject->getType()
        );
    }
}
