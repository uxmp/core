<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage\Update;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class RecursiveFileReaderTest extends MockeryTestCase
{
    private RecursiveFileReader $subject;

    public function setUp(): void
    {
        $this->subject = new RecursiveFileReader();
    }

    public function testReadReturnsEmptyArrayOnScandirError(): void
    {
        $this->assertSame(
            [],
            iterator_to_array($this->subject->read('/foobar'))
        );
    }

    public function testReadReturnsFile(): void
    {
        $this->assertContains(
            __FILE__,
            $this->subject->read(__DIR__ . '/../../')
        );
    }
}
