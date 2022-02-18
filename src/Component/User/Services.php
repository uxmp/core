<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\User;

use function DI\autowire;

return [
    UserCreatorInterface::class => autowire(UserCreator::class),
    PasswordVerificatorInterface::class => autowire(PasswordVerificator::class),
];
