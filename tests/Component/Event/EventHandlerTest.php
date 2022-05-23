<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Event;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;

class EventHandlerTest extends MockeryTestCase
{
    private MockInterface $dic;

    private EventHandler $subject;

    public function setUp(): void
    {
        $this->dic = Mockery::mock(ContainerInterface::class);

        $this->subject = new EventHandler(
            $this->dic
        );
    }

    public function testRunRunsFiredEvents(): void
    {
        $event = function (ContainerInterface $c): void {
            $this->assertInstanceOf(
                ContainerInterface::class,
                $c
            );
        };

        $this->subject->fire($event);

        $this->subject->run();
    }
}
