<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Application;
use Ahc\Cli\IO\Interactor;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Catalog\Manage\CatalogCleanerInterface;

class CatalogCleanCommandTest extends MockeryTestCase
{
    private MockInterface $dic;

    private MockInterface $app;

    private CatalogCleanCommand $subject;

    public function setUp(): void
    {
        $this->dic = Mockery::mock(ContainerInterface::class);
        $this->app = Mockery::mock(Application::class);

        $this->subject = new CatalogCleanCommand(
            $this->dic
        );
        $this->subject->bind($this->app);
    }

    public function testExecuteExecutes(): void
    {
        $catalogCleaner = Mockery::mock(CatalogCleanerInterface::class);
        $interactor = Mockery::mock(Interactor::class);

        $catalogId = 666;

        $this->dic->shouldReceive('get')
            ->with(CatalogCleanerInterface::class)
            ->once()
            ->andReturn($catalogCleaner);

        $catalogCleaner->shouldReceive('clean')
            ->with($interactor, $catalogId)
            ->once();

        $this->app->shouldReceive('io')
            ->withNoArgs()
            ->once()
            ->andReturn($interactor);

        $this->subject->execute($catalogId);
    }
}
