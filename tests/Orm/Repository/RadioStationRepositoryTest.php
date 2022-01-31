<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\RadioStation;
use Uxmp\Core\Orm\Model\RadioStationInterface;

class RadioStationRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private RadioStationRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = \Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = \Mockery::mock(ClassMetadata::class);

        $this->subject = new RadioStationRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsRadioStation(): void
    {
        $this->assertInstanceOf(
            RadioStation::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $station = \Mockery::mock(RadioStationInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($station)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($station);
    }
}
