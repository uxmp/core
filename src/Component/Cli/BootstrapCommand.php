<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Input\Command;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Setup\BootstrapperInterface;

final class BootstrapCommand extends Command
{
    public function __construct(
        private ContainerInterface $dic,
    ) {
        parent::__construct(
            'setup:bootstrap',
            'Bootstraps uxmp'
        );

        $this
            ->usage(
                '<bold>  $0 sb</end> <comment></end> ## Bootstraps uxmp<eol/>'
            );
    }

    public function execute(): void
    {
        $this->dic->get(BootstrapperInterface::class)->bootstrap($this->app()?->io());
    }
}
