<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Application;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Art\ArtUpdaterInterface;

class ArtUpdateCommandTest extends MockeryTestCase
{
    private MockInterface $dic;

    private MockInterface $app;

    private ArtUpdateCommand $subject;

    public function setUp(): void
    {
        $this->dic = Mockery::mock(ContainerInterface::class);
        $this->app = Mockery::mock(Application::class);

        $this->subject = new ArtUpdateCommand(
            $this->dic
        );
        $this->subject->bind($this->app);
    }

    public function testExecuteExecutes(): void
    {
        $artUpdater = Mockery::mock(ArtUpdaterInterface::class);

        $catalogId= 666;

        $this->dic->shouldReceive('get')
            ->with(ArtUpdaterInterface::class)
            ->once()
            ->andReturn($artUpdater);

        $artUpdater->shouldReceive('update')
            ->with($catalogId)
            ->once();

        $this->subject->execute($catalogId);
    }
}
