<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\Catalog;
use Uxmp\Core\Orm\Model\CatalogInterface;

class CatalogRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private CatalogRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new CatalogRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsCatalog(): void
    {
        $this->assertInstanceOf(
            Catalog::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $catalog = Mockery::mock(CatalogInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($catalog)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($catalog);
    }

    public function testDeleteDeletes(): void
    {
        $catalog = Mockery::mock(CatalogInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($catalog)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($catalog);
    }
}
