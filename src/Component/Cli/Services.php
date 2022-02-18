<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use function DI\autowire;

return [
    Wizard\UserCreationWizardInterface::class => autowire(Wizard\UserCreationWizard::class),
];
