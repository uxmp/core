<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Disc;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;

class DiscLengthUpdaterTest extends MockeryTestCase
{
    private MockInterface $discRepository;

    private DiscLengthUpdater $subject;

    public function setUp(): void
    {
        $this->discRepository = \Mockery::mock(DiscRepositoryInterface::class);

        $this->subject = new DiscLengthUpdater(
            $this->discRepository
        );
    }

    public function testUpdateUpdates(): void
    {
        $disc = \Mockery::mock(DiscInterface::class);
        $song = \Mockery::mock(SongInterface::class);

        $length1 = 666;
        $length2 = 42;

        $disc->shouldReceive('getSongs')
            ->withNoArgs()
            ->once()
            ->andReturn([$song, $song]);
        $disc->shouldReceive('setLength')
            ->with($length1 + $length2)
            ->once();

        $song->shouldReceive('getLength')
            ->withNoArgs()
            ->twice()
            ->andReturn($length1, $length2);

        $this->discRepository->shouldReceive('save')
            ->with($disc)
            ->once();

        $this->subject->update($disc);
    }
}
