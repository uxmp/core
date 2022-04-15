<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Art\ArtContentRetrieverInterface;
use Uxmp\Core\Component\Art\Exception\ArtContentException;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class CoverArtDataProviderTest extends MockeryTestCase
{
    private MockInterface $artistRepository;

    private MockInterface $artContentRetriever;

    private CoverArtDataProvider $subject;

    public function setUp(): void
    {
        $this->artistRepository = Mockery::mock(ArtistRepositoryInterface::class);
        $this->artContentRetriever = Mockery::mock(ArtContentRetrieverInterface::class);

        $this->subject = new CoverArtDataProvider(
            $this->artistRepository,
            $this->artContentRetriever,
        );
    }

    public function testGetArtReturnsEmptyDataIfArtDoesNotExist(): void
    {
        $itemId = 666;
        $covertArtId = 'artist-'.$itemId;

        $artist = Mockery::mock(ArtistInterface::class);

        $this->artistRepository->shouldReceive('find')
            ->with($itemId)
            ->once()
            ->andReturn($artist);

        $this->artContentRetriever->shouldReceive('retrieve')
            ->with($artist)
            ->once()
            ->andThrow(new ArtContentException());

        $this->assertSame(
            ['art' => '', 'contentType' => ''],
            $this->subject->getArt($covertArtId)
        );
    }

    public function testGetArtReturnsData(): void
    {
        $itemId = 666;
        $covertArtId = 'artist-'.$itemId;
        $content = 'some-content';
        $contentype = 'some/type';

        $artist = Mockery::mock(ArtistInterface::class);

        $this->artistRepository->shouldReceive('find')
            ->with($itemId)
            ->once()
            ->andReturn($artist);

        $this->artContentRetriever->shouldReceive('retrieve')
            ->with($artist)
            ->once()
            ->andReturn([
                'content' => $content,
                'mimeType' => $contentype,
            ]);

        $this->assertSame(
            ['art' => $content, 'contentType' => $contentype],
            $this->subject->getArt($covertArtId)
        );
    }
}
