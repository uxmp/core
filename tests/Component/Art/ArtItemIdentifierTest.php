<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class ArtItemIdentifierTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $artistRepository;

    private ArtItemIdentifier $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->artistRepository = Mockery::mock(ArtistRepositoryInterface::class);

        $this->subject = new ArtItemIdentifier(
            $this->albumRepository,
            $this->artistRepository,
        );
    }

    public function testIdentifyReturnsNullIfStringFormatDoesNotPass(): void
    {
        $this->assertNull(
            $this->subject->identify('snafu')
        );
    }

    public function testIdentifyReturnsNullIfNotIdentifyAble(): void
    {
        $this->assertNull(
            $this->subject->identify('snafu-666')
        );
    }

    public function typeDataProvider(): array
    {
        return [
            ['artistRepository', 'artist', 666],
            ['albumRepository', 'album', 42],
        ];
    }

    /**
     * @dataProvider typeDataProvider
     */
    public function testIdentifyReturnsItem(
        string $repo,
        string $type,
        int $id
    ): void {
        $item = Mockery::mock(CachableArtItemInterface::class);

        $this->{$repo}->shouldReceive('find')
            ->with($id)
            ->once()
            ->andReturn($item);

        $this->assertSame(
            $item,
            $this->subject->identify(sprintf('%s-%d', $type, $id))
        );
    }
}
