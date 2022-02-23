<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Application;
use Ahc\Cli\IO\Interactor;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Cli\Wizard\UserCreationWizardInterface;

class UserAddCommandTest extends MockeryTestCase
{
    private MockInterface $dic;

    private MockInterface $app;

    private UserAddCommand $subject;

    public function setUp(): void
    {
        $this->dic = Mockery::mock(ContainerInterface::class);
        $this->app = Mockery::mock(Application::class);

        $this->subject = new UserAddCommand(
            $this->dic
        );
        $this->subject->bind($this->app);
    }

    public function testExecuteExecutes(): void
    {
        $userCreationWized = Mockery::mock(UserCreationWizardInterface::class);
        $interactor = Mockery::mock(Interactor::class);

        $username = 'some-name';

        $this->dic->shouldReceive('get')
            ->with(UserCreationWizardInterface::class)
            ->once()
            ->andReturn($userCreationWized);

        $userCreationWized->shouldReceive('create')
            ->with($interactor, $username)
            ->once();

        $this->app->shouldReceive('io')
            ->withNoArgs()
            ->once()
            ->andReturn($interactor);

        $this->subject->execute($username);
    }
}
