<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\Session;
use Uxmp\Core\Orm\Model\SessionInterface;

class SessionRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private SessionRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = \Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = \Mockery::mock(ClassMetadata::class);

        $this->subject = new SessionRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsSession(): void
    {
        $this->assertInstanceOf(
            Session::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $session = \Mockery::mock(SessionInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($session)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($session);
    }

    public function testDeleteDeletes(): void
    {
        $session = \Mockery::mock(SessionInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($session)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($session);
    }
}
