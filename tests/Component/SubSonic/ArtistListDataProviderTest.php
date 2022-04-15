<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class ArtistListDataProviderTest extends MockeryTestCase
{
    private MockInterface $artistRepository;

    private ArtistListDataProvider $subject;

    public function setUp(): void
    {
        $this->artistRepository = Mockery::mock(ArtistRepositoryInterface::class);

        $this->subject = new ArtistListDataProvider(
            $this->artistRepository,
        );
    }

    public function testGetIgnoredArticlesReturnEmptyArray(): void
    {
        $this->assertSame(
            [],
            $this->subject->getIgnoredArticles()
        );
    }

    public function testGetArtistsYieldsData(): void
    {
        $artist = Mockery::mock(ArtistInterface::class);

        $id = 666;
        $name = 'some-name';
        $albumCount = 42;

        $this->artistRepository->shouldReceive('findBy')
            ->with([], ['title' => 'ASC'])
            ->once()
            ->andReturn([$artist]);

        $artist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($id);
        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($name);
        $artist->shouldReceive('getAlbumCount')
            ->withNoArgs()
            ->once()
            ->andReturn($albumCount);

        $this->assertSame(
            [[
                'id' => (string) $id,
                'name' => $name,
                'artistImageUrl' => '',
                'coverArtId' => 'artist-'.$id,
                'albumCount' => $albumCount,
                'starred' => null,
            ]],
            iterator_to_array($this->subject->getArtists(null))
        );
    }
}
