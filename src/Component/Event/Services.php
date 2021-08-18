<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Event;

use function DI\autowire;

return [
    EventHandlerInterface::class => autowire(EventHandler::class),
];
