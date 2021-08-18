<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Session;

use function DI\autowire;

return [
    JwtManagerInterface::class => autowire(JwtManager::class),
    SessionValidatorMiddleware::class => autowire(),
    SessionManagerInterface::class => autowire(SessionManager::class),
];
