<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Art\ArtContentRetrieverInterface;
use Uxmp\Core\Component\Art\ArtItemIdentifierInterface;
use Uxmp\Core\Component\Art\CachableArtItemInterface;
use Uxmp\Core\Component\Art\Exception\ArtContentException;

class CoverArtDataProviderTest extends MockeryTestCase
{
    private MockInterface $artContentRetriever;

    private MockInterface $artItemIdentifier;

    private CoverArtDataProvider $subject;

    public function setUp(): void
    {
        $this->artItemIdentifier = Mockery::mock(ArtItemIdentifierInterface::class);
        $this->artContentRetriever = Mockery::mock(ArtContentRetrieverInterface::class);

        $this->subject = new CoverArtDataProvider(
            $this->artContentRetriever,
            $this->artItemIdentifier,
        );
    }

    public function testGetArtReturnsEmptyDataIfNotIdentifyable(): void
    {
        $covertArtId = 'some-art-id';

        $this->artItemIdentifier->shouldReceive('identify')
            ->with($covertArtId)
            ->once()
            ->andReturnNull();

        $this->assertSame(
            ['art' => '', 'contentType' => ''],
            $this->subject->getArt($covertArtId)
        );
    }

    public function testGetArtReturnsEmptyDataIfArtDoesNotExist(): void
    {
        $covertArtId = 'some-art-id';

        $item = Mockery::mock(CachableArtItemInterface::class);

        $this->artItemIdentifier->shouldReceive('identify')
            ->with($covertArtId)
            ->once()
            ->andReturn($item);

        $this->artContentRetriever->shouldReceive('retrieve')
            ->with($item)
            ->once()
            ->andThrow(new ArtContentException());

        $this->assertSame(
            ['art' => '', 'contentType' => ''],
            $this->subject->getArt($covertArtId)
        );
    }

    public function testGetArtReturnsData(): void
    {
        $content = 'some-content';
        $contentype = 'some/type';
        $covertArtId = 'some-art-id';

        $item = Mockery::mock(CachableArtItemInterface::class);

        $this->artItemIdentifier->shouldReceive('identify')
            ->with($covertArtId)
            ->once()
            ->andReturn($item);

        $this->artContentRetriever->shouldReceive('retrieve')
            ->with($item)
            ->once()
            ->andReturn([
                'content' => $content,
                'mimeType' => $contentype,
            ]);

        $this->assertSame(
            ['art' => $content, 'contentType' => $contentype],
            $this->subject->getArt($covertArtId)
        );
    }
}
