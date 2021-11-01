<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Setup;

use function DI\autowire;

return [
    BootstrapperInterface::class => autowire(Bootstrapper::class)->constructorParameter(
        'validators',
        [
            autowire(Validator\LogPathValidator::class),
            autowire(Validator\AssetPathValidator::class),
            autowire(Validator\DatabaseValidator::class),
        ]
    ),
];
