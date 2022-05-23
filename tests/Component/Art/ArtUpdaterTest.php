<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Artist\ArtistCoverUpdaterInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class ArtUpdaterTest extends MockeryTestCase
{
    private MockInterface $artistRepository;

    private MockInterface $artistCoverUpdater;

    private ArtUpdater $subject;

    public function setUp(): void
    {
        $this->artistRepository = Mockery::mock(ArtistRepositoryInterface::class);
        $this->artistCoverUpdater = Mockery::mock(ArtistCoverUpdaterInterface::class);

        $this->subject = new ArtUpdater(
            $this->artistRepository,
            $this->artistCoverUpdater,
        );
    }

    public function testUpdateUpdates(): void
    {
        $catalogId = 666;

        $artist = Mockery::mock(ArtistInterface::class);

        $this->artistRepository->shouldReceive('findAll')
            ->withNoArgs()
            ->once()
            ->andReturn([$artist]);

        $this->artistCoverUpdater->shouldReceive('update')
            ->with($artist)
            ->once();

        $this->subject->update($catalogId);
    }
}
