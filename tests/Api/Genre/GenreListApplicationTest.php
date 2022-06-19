<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Genre;

use Generator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Orm\Repository\GenreRepositoryInterface;

class GenreListApplicationTest extends MockeryTestCase
{
    private MockInterface $genreRepository;

    private GenreListApplication $subject;

    public function setUp(): void
    {
        $this->genreRepository = Mockery::mock(GenreRepositoryInterface::class);

        $this->subject = new GenreListApplication(
            $this->genreRepository
        );
    }

    public function testRunReturnsOutput(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $genreId = 666;
        $name = 'some-name';
        $albumCount = 42;
        $songCount = 33;

        $generator = function (mixed $item): Generator {
            yield $item;
        };

        $this->genreRepository->shouldReceive('getGenreStatistics')
            ->withNoArgs()
            ->once()
            ->andReturn($generator([
                'id' => $genreId,
                'value' => $name,
                'albumCount' => $albumCount,
                'songCount' => $songCount,
            ]));

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(
                json_encode(
                    ['items' => [[
                        'id' => $genreId,
                        'name' => $name,
                        'albumCount' => $albumCount,
                        'songCount' => $songCount,
                    ]]],
                    JSON_PRETTY_PRINT
                )
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
