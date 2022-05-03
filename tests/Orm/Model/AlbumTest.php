<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Mockery;

class AlbumTest extends ModelTestCase
{
    /** @var mixed|Album */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new Album();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Title', 'some-title'],
            ['Artist', \Mockery::mock(ArtistInterface::class)],
            ['Mbid', 'some-mbid'],
            ['Catalog', \Mockery::mock(CatalogInterface::class)],
            ['LastModified', new \DateTime()],
        ];
    }

    public function testGetDiscsReturnsData(): void
    {
        $this->assertIsIterable(
            $this->subject->getDiscs()
        );
    }

    public function testGetDiscCountReturnsValus(): void
    {
        $this->assertSame(
            0,
            $this->subject->getDiscCount()
        );
    }

    public function testGetArtItemTypeReturnsAlbum(): void
    {
        $this->assertSame(
            'album',
            $this->subject->getArtItemType()
        );
    }

    public function testGetArtItemIdReturnsMbid(): void
    {
        $value = 'some-value';

        $this->subject->setMbid($value);

        $this->assertSame(
            $value,
            $this->subject->getArtItemId()
        );
    }

    public function testGetTypeReturnsType(): void
    {
        $this->assertSame(
            'album',
            $this->subject->getType()
        );
    }

    public function testGetLengthReturnsValue(): void
    {
        $disc = Mockery::mock(DiscInterface::class);

        $length = 666;

        $this->subject->addDisc($disc);

        $disc->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($length);

        $this->assertSame(
            $length,
            $this->subject->getLength()
        );
    }

    public function testGetSongCountReturnsValue(): void
    {
        $disc = Mockery::mock(DiscInterface::class);

        $count = 666;

        $this->subject->addDisc($disc);

        $disc->shouldReceive('getSongCount')
            ->withNoArgs()
            ->once()
            ->andReturn($count);

        $this->assertSame(
            $count,
            $this->subject->getSongCount()
        );
    }
}
