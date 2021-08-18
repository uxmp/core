<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Config;

use function DI\autowire;

return [
    ConfigProviderInterface::class => autowire(ConfigProvider::class),
];
