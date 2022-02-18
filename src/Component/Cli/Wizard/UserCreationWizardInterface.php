<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli\Wizard;

use Ahc\Cli\IO\Interactor;

interface UserCreationWizardInterface
{
    public function create(
        Interactor $io,
        string $username,
    ): void;
}
