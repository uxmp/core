<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Exception;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class AccessViolationTest extends MockeryTestCase
{
    private AccessViolation $subject;

    public function setUp(): void
    {
        $this->subject = new AccessViolation();
    }

    public function testGetMessgeReturnsValue(): void
    {
        $this->assertSame(
            'Access denied',
            $this->subject->getMessage()
        );
    }
}
