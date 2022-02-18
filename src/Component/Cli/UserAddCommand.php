<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Input\Command;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Cli\Wizard\UserCreationWizardInterface;

final class UserAddCommand extends Command
{
    public function __construct(
        private ContainerInterface $dic
    ) {
        parent::__construct(
            'user:add',
            'Adds an user'
        );

        $this
            ->argument(
                '<username>',
                'User name'
            )
            ->usage(
                '<bold>  $0 cl</end> <comment></end> ## Adds an user<eol/>'
            );
    }

    public function execute(?string $username): void
    {
        $this->dic->get(UserCreationWizardInterface::class)->create(
            $this->app()?->io(),
            (string) $username
        );
    }
}
