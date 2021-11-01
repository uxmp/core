<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Application;
use Ahc\Cli\IO\Interactor;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Catalog\Manage\CatalogAdderInterface;
use Uxmp\Core\Component\Catalog\Manage\CatalogUpdaterInterface;

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

        $catalogId = 666;

        $this->dic->shouldReceive('get')
            ->with(CatalogUpdaterInterface::class)
            ->once()
            ->andReturn($catalogUpdater);

        $catalogUpdater->shouldReceive('update')
            ->with($interactor, $catalogId)
            ->once();

        $this->app->shouldReceive('io')
            ->withNoArgs()
            ->once()
            ->andReturn($interactor);

        $this->subject->execute($catalogId);
    }
}
