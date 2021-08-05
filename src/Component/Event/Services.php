<?php

declare(strict_types=1);

namespace Usox\Core\Component\Event;

use function DI\autowire;

return [
    EventHandlerInterface::class => autowire(EventHandler::class),
];
