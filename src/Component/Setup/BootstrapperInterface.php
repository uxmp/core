<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Setup;

use Ahc\Cli\IO\Interactor;

interface BootstrapperInterface
{
    public function bootstrap(Interactor $io): void;
}
