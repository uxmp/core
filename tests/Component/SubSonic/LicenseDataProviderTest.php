<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class LicenseDataProviderTest extends MockeryTestCase
{
    private LicenseDataProvider $subject;

    public function setUp(): void
    {
        $this->subject = new LicenseDataProvider();
    }

    public function testIsValidReturnsTrue(): void
    {
        $this->assertTrue(
            $this->subject->isValid()
        );
    }

    public function testGetEmailAddressReturnsDummyValue(): void
    {
        $this->assertSame(
            'no-licence-required@example.com',
            $this->subject->getEmailAddress()
        );
    }

    public function testGetExpiryDateReturnsDateInFuture(): void
    {
        $this->assertGreaterThan(
            time(),
            $this->subject->getExpiryDate()->getTimestamp()
        );
    }
}
