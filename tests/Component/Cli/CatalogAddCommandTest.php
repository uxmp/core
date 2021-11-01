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

class CatalogAddCommandTest extends MockeryTestCase
{
    private MockInterface $dic;

    private MockInterface $app;

    private CatalogAddCommand $subject;

    public function setUp(): void
    {
        $this->dic = Mockery::mock(ContainerInterface::class);
        $this->app = Mockery::mock(Application::class);

        $this->subject = new CatalogAddCommand(
            $this->dic
        );
        $this->subject->bind($this->app);
    }

    public function testExecuteExecutes(): void
    {
        $catalogAdder = Mockery::mock(CatalogAdderInterface::class);
        $interactor = Mockery::mock(Interactor::class);

        $path = 'some-path';

        $this->dic->shouldReceive('get')
            ->with(CatalogAdderInterface::class)
            ->once()
            ->andReturn($catalogAdder);

        $catalogAdder->shouldReceive('add')
            ->with($interactor, $path)
            ->once();

        $this->app->shouldReceive('io')
            ->withNoArgs()
            ->once()
            ->andReturn($interactor);

        $this->subject->execute($path);
    }
}
