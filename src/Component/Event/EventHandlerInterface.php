<?php

namespace Uxmp\Core\Component\Event;

use Psr\Container\ContainerInterface;

interface EventHandlerInterface
{
    /**
     * @param callable(ContainerInterface): void $event
     */
    public function fire(
        callable $event
    ): void;

    public function run(): void;
}
