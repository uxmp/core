<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Authentication;

use function DI\autowire;

return [
    JwtManagerInterface::class => autowire(JwtManager::class),
    SessionManagerInterface::class => autowire(SessionManager::class),
];
