<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\Favorite;
use Uxmp\Core\Orm\Model\FavoriteInterface;

class FavoriteRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private FavoriteRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new FavoriteRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsFavorite(): void
    {
        $this->assertInstanceOf(
            Favorite::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $favorite = Mockery::mock(FavoriteInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($favorite)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($favorite);
    }

    public function testDeleteDeletes(): void
    {
        $favorite = Mockery::mock(FavoriteInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($favorite)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($favorite);
    }
}
