<?php

declare(strict_types=1);

namespace Uxmp\Core\Api;

use function DI\autowire;

return [
    Lib\ResultItemFactoryInterface::class => autowire(Lib\ResultItemFactory::class),
    ApiApplication::class => autowire(),
];
