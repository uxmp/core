<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class PingDataProviderTest extends MockeryTestCase
{
    private PingDataProvider $subject;

    public function setUp(): void
    {
        $this->subject = new PingDataProvider();
    }

    public function testIsOkReturnsTrue(): void
    {
        $this->assertTrue(
            $this->subject->isOk()
        );
    }
}
