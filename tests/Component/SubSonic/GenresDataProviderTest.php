<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Generator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Repository\GenreRepositoryInterface;

class GenresDataProviderTest extends MockeryTestCase
{
    private MockInterface $genreRepository;

    private GenresDataProvider $subject;

    public function setUp(): void
    {
        $this->genreRepository = Mockery::mock(GenreRepositoryInterface::class);

        $this->subject = new GenresDataProvider(
            $this->genreRepository,
        );
    }

    public function testGetGenresReturnsGenerator(): void
    {
        $item = 666;

        $generator = function (mixed $item): Generator {
            yield $item;
        };

        $this->genreRepository->shouldReceive('getGenreStatistics')
            ->withNoArgs()
            ->once()
            ->andReturn($generator($item));

        $this->assertSame(
            [$item],
            iterator_to_array($this->subject->getGenres())
        );
    }
}
