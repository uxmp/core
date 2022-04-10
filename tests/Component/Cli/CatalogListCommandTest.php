<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Application;
use Ahc\Cli\IO\Interactor;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Catalog\Manage\CatalogListerInterface;

class CatalogListCommandTest extends MockeryTestCase
{
    private MockInterface $dic;

    private MockInterface $app;

    private CatalogListCommand $subject;

    public function setUp(): void
    {
        $this->dic = Mockery::mock(ContainerInterface::class);
        $this->app = Mockery::mock(Application::class);

        $this->subject = new CatalogListCommand(
            $this->dic
        );
        $this->subject->bind($this->app);
    }

    public function testExecuteExecutes(): void
    {
        $catalogLister = Mockery::mock(CatalogListerInterface::class);
        $interactor = Mockery::mock(Interactor::class);

        $this->dic->shouldReceive('get')
            ->with(CatalogListerInterface::class)
            ->once()
            ->andReturn($catalogLister);

        $catalogLister->shouldReceive('list')
            ->with($interactor)
            ->once();

        $this->app->shouldReceive('io')
            ->withNoArgs()
            ->once()
            ->andReturn($interactor);

        $this->subject->execute();
    }
}
