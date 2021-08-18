<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Event;

use Psr\Container\ContainerInterface;

final class EventHandler implements EventHandlerInterface
{
    /** @var array<array{type: int, context: array<mixed>}> */
    private array $events = [];

    public function __construct(
        private ContainerInterface $dic
    ) {
    }

    public function fire(
        callable $event
    ): void {
        $this->events[] = $event;
    }

    public function run(): void
    {
        foreach ($this->events as $event) {
            $event($this->dic);
        }
    }
}
