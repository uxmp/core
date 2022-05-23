<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag\Extractor;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;

class ExtractorDeterminatorTest extends MockeryTestCase
{
    private MockInterface $extractor;

    private ExtractorDeterminator $subject;

    public function setUp(): void
    {
        $this->extractor = Mockery::mock(ExtractorInterface::class);

        $this->subject = new ExtractorDeterminator([$this->extractor]);
    }

    public function testDetermineReturnsNullIfNoApplies(): void
    {
        $data = ['some-data'];

        $this->extractor->shouldReceive('applies')
            ->with($data)
            ->once()
            ->andReturnFalse();

        $this->assertNull(
            $this->subject->determine($data)
        );
    }

    public function testDetermineReturnsExtractorIfApplies(): void
    {
        $data = ['some-data'];

        $this->extractor->shouldReceive('applies')
            ->with($data)
            ->once()
            ->andReturnTrue();

        $this->assertSame(
            $this->extractor,
            $this->subject->determine($data)
        );
    }
}
