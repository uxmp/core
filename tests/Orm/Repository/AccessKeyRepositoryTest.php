<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\AccessKey;
use Uxmp\Core\Orm\Model\AccessKeyInterface;

class AccessKeyRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private AccessKeyRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new AccessKeyRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsAccessKey(): void
    {
        $this->assertInstanceOf(
            AccessKey::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $accessKey = Mockery::mock(AccessKeyInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($accessKey)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($accessKey);
    }

    public function testDeleteDeletes(): void
    {
        $accessKey = Mockery::mock(AccessKeyInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($accessKey)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($accessKey);
    }
}
