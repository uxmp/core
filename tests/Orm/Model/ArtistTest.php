<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

class ArtistTest extends ModelTestCase
{
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new Artist();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Title', 'some-title'],
            ['Mbid', 'some-mbid'],
            ['LastModified', new \DateTime()],
        ];
    }

    public function testGetArtItemTypeReturnsArtist(): void
    {
        $this->assertSame(
            'artist',
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

    public function testGetAlbumsReturnsAddedAlbum(): void
    {
        $album = \Mockery::mock(AlbumInterface::class);

        $this->subject->addAlbum($album);

        $this->assertSame(
            [$album],
            iterator_to_array($this->subject->getAlbums())
        );
    }
}
