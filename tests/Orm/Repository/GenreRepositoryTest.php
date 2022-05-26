<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\Genre;
use Uxmp\Core\Orm\Model\GenreInterface;

class GenreRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private GenreRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new GenreRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsGenre(): void
    {
        $this->assertInstanceOf(
            Genre::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $genre = Mockery::mock(GenreInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($genre)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($genre);
    }

    public function testDeleteDeletes(): void
    {
        $genre = Mockery::mock(GenreInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($genre)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($genre);
    }
}
