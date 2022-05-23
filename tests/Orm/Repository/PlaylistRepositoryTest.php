<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\Playlist;
use Uxmp\Core\Orm\Model\PlaylistInterface;

class PlaylistRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private PlaylistRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new PlaylistRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsPlaylist(): void
    {
        $this->assertInstanceOf(
            Playlist::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($playlist)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($playlist);
    }

    public function testDeleteDeletes(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($playlist)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($playlist);
    }
}
