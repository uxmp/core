<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Scanner;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Disc\DiscLengthUpdaterInterface;
use Uxmp\Core\Component\Event\EventHandlerInterface;
use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;

class DiscCacheTest extends MockeryTestCase
{
    private MockInterface $discRepository;

    private MockInterface $albumCache;

    private MockInterface $eventHandler;

    private DiscCache $subject;

    public function setUp(): void
    {
        $this->discRepository = Mockery::mock(DiscRepositoryInterface::class);
        $this->albumCache = Mockery::mock(AlbumCacheInterface::class);
        $this->eventHandler = Mockery::mock(EventHandlerInterface::class);

        $this->subject = new DiscCache(
            $this->discRepository,
            $this->albumCache,
            $this->eventHandler,
        );
    }

    public function testRetrieveReturnsCreatedDisc(): void
    {
        $catalog = Mockery::mock(CatalogInterface::class);
        $audioFile = Mockery::mock(AudioFileInterface::class);
        $disc = Mockery::mock(DiscInterface::class);
        $album = Mockery::mock(AlbumInterface::class);

        $analysisResult = ['some-result'];
        $discMbid = 'some-disc-mbid';
        $discNumber = 666;

        $audioFile->shouldReceive('getDiscMbid')
            ->withNoArgs()
            ->once()
            ->andReturn($discMbid);
        $audioFile->shouldReceive('getDiscNumber')
            ->withNoArgs()
            ->once()
            ->andReturn($discNumber);

        $this->discRepository->shouldReceive('findUniqueDisc')
            ->with($discMbid, $discNumber)
            ->once()
            ->andReturnNull();
        $this->discRepository->shouldReceive('prototype')
            ->withNoArgs()
            ->once()
            ->andReturn($disc);
        $this->discRepository->shouldReceive('save')
            ->with($disc)
            ->once();

        $this->albumCache->shouldReceive('retrieve')
            ->with($catalog, $audioFile, $analysisResult)
            ->once()
            ->andReturn($album);

        $disc->shouldReceive('setMbid')
            ->with($discMbid)
            ->once()
            ->andReturnSelf();
        $disc->shouldReceive('setAlbum')
            ->with($album)
            ->once()
            ->andReturnSelf();
        $disc->shouldReceive('setNumber')
            ->with($discNumber)
            ->once()
            ->andReturnSelf();

        $this->eventHandler->shouldReceive('fire')
            ->with(Mockery::on(function (callable $fun) use ($disc): bool {
                $dic = Mockery::mock(ContainerInterface::class);
                $updater = Mockery::mock(DiscLengthUpdaterInterface::class);

                $dic->shouldReceive('get')
                    ->with(DiscLengthUpdaterInterface::class)
                    ->once()
                    ->andReturn($updater);

                $updater->shouldReceive('update')
                    ->with($disc)
                    ->once();

                $fun($dic);

                return true;
            }))
            ->once();

        $this->assertSame(
            $disc,
            $this->subject->retrieve($catalog, $audioFile, $analysisResult)
        );
    }

    public function testRetrieveUpdatesAndReturnsExistingDisc(): void
    {
        $catalog = Mockery::mock(CatalogInterface::class);
        $audioFile = Mockery::mock(AudioFileInterface::class);
        $disc = Mockery::mock(DiscInterface::class);
        $album = Mockery::mock(AlbumInterface::class);

        $analysisResult = ['some-result'];
        $discMbid = 'some-disc-mbid';
        $discNumber = 666;

        $audioFile->shouldReceive('getDiscMbid')
            ->withNoArgs()
            ->twice()
            ->andReturn($discMbid);
        $audioFile->shouldReceive('getDiscNumber')
            ->withNoArgs()
            ->twice()
            ->andReturn($discNumber);

        $this->discRepository->shouldReceive('findUniqueDisc')
            ->with($discMbid, $discNumber)
            ->once()
            ->andReturn($disc);
        $this->discRepository->shouldReceive('save')
            ->with($disc)
            ->once();

        $this->albumCache->shouldReceive('retrieve')
            ->with($catalog, $audioFile, $analysisResult)
            ->twice()
            ->andReturn($album);

        $disc->shouldReceive('setMbid')
            ->with($discMbid)
            ->once()
            ->andReturnSelf();
        $disc->shouldReceive('setAlbum')
            ->with($album)
            ->once()
            ->andReturnSelf();
        $disc->shouldReceive('setNumber')
            ->with($discNumber)
            ->once()
            ->andReturnSelf();

        $this->eventHandler->shouldReceive('fire')
            ->with(Mockery::on(function (callable $fun) use ($disc): bool {
                $dic = Mockery::mock(ContainerInterface::class);
                $updater = Mockery::mock(DiscLengthUpdaterInterface::class);

                $dic->shouldReceive('get')
                    ->with(DiscLengthUpdaterInterface::class)
                    ->once()
                    ->andReturn($updater);

                $updater->shouldReceive('update')
                    ->with($disc)
                    ->once();

                $fun($dic);

                return true;
            }))
            ->twice();

        // test cache
        $this->subject->retrieve($catalog, $audioFile, $analysisResult);

        $this->assertSame(
            $disc,
            $this->subject->retrieve($catalog, $audioFile, $analysisResult)
        );
    }
}
