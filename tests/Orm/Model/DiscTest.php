<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

class DiscTest extends ModelTestCase
{
    /** @var mixed|Disc */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new Disc();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Mbid', 'some-mbid'],
            ['AlbumId', 666],
            ['Number', 42],
            ['Album', \Mockery::mock(AlbumInterface::class)],
            ['Length', 33],
        ];
    }

    public function testGetSongsReturnsAddedSong(): void
    {
        $song = \Mockery::mock(SongInterface::class);

        $this->subject->addSong($song);

        $this->assertSame(
            [$song],
            iterator_to_array($this->subject->getSongs())
        );
    }
}
