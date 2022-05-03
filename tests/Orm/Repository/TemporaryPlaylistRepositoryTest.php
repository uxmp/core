<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\TemporaryPlaylist;
use Uxmp\Core\Orm\Model\TemporaryPlaylistInterface;

class TemporaryPlaylistRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private TemporaryPlaylistRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new TemporaryPlaylistRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsAccessKey(): void
    {
        $this->assertInstanceOf(
            TemporaryPlaylist::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $temporaryPlaylist = Mockery::mock(TemporaryPlaylistInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($temporaryPlaylist)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($temporaryPlaylist);
    }

    public function testDeleteDeletes(): void
    {
        $temporaryPlaylist = Mockery::mock(TemporaryPlaylistInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($temporaryPlaylist)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($temporaryPlaylist);
    }
}
