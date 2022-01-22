<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Application;
use Ahc\Cli\IO\Interactor;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Catalog\Manage\CatalogUpdaterInterface;
use Uxmp\Core\Component\Event\EventHandlerInterface;

class CatalogUpdateCommandTest extends MockeryTestCase
{
    private MockInterface $dic;

    private MockInterface $app;

    private CatalogUpdateCommand $subject;

    public function setUp(): void
    {
        $this->dic = Mockery::mock(ContainerInterface::class);
        $this->app = Mockery::mock(Application::class);

        $this->subject = new CatalogUpdateCommand(
            $this->dic
        );
        $this->subject->bind($this->app);
    }

    public function testExecuteExecutes(): void
    {
        $catalogUpdater = Mockery::mock(CatalogUpdaterInterface::class);
        $interactor = Mockery::mock(Interactor::class);
        $eventHandler = Mockery::mock(EventHandlerInterface::class);

        $catalogId = 666;

        $this->dic->shouldReceive('get')
            ->with(CatalogUpdaterInterface::class)
            ->once()
            ->andReturn($catalogUpdater);
        $this->dic->shouldReceive('get')
            ->with(EventHandlerInterface::class)
            ->once()
            ->andReturn($eventHandler);

        $catalogUpdater->shouldReceive('update')
            ->with($interactor, $catalogId)
            ->once();

        $this->app->shouldReceive('io')
            ->withNoArgs()
            ->once()
            ->andReturn($interactor);

        $eventHandler->shouldReceive('run')
            ->withNoArgs()
            ->once();

        $this->subject->execute($catalogId);
    }
}
