<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

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
}
