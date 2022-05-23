<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\User;
use Uxmp\Core\Orm\Model\UserInterface;

class UserRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private UserRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new UserRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnUser(): void
    {
        $this->assertInstanceOf(
            User::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $user = Mockery::mock(UserInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($user)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($user);
    }

    public function testDeleteDeletes(): void
    {
        $user = Mockery::mock(UserInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($user)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($user);
    }
}
