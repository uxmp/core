<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Generator;
use JsonSerializable;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Model\GenreInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\GenreRepositoryInterface;

class AlbumListByGenreApplicationTest extends Mockery\Adapter\Phpunit\MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $genreRepository;

    private MockInterface $resultItemFactory;

    private AlbumListByGenreApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->genreRepository = Mockery::mock(GenreRepositoryInterface::class);
        $this->resultItemFactory = Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new AlbumListByGenreApplication(
            $this->albumRepository,
            $this->genreRepository,
            $this->resultItemFactory,
        );
    }

    public function testRunReturnsNotFoundIfGenreIsNotKnown(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);

        $genreId = 666;

        $this->genreRepository->shouldReceive('find')
            ->with($genreId)
            ->once()
            ->andReturnNull();

        $response->shouldReceive('withStatus')
            ->with(StatusCode::NOT_FOUND)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['genreId' => (string) $genreId])
        );
    }

    public function testRunReturnsList(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $item = Mockery::mock(JsonSerializable::class);
        $genre = Mockery::mock(GenreInterface::class);

        $data = ['some-data'];
        $genreId = 666;

        $this->genreRepository->shouldReceive('find')
            ->with($genreId)
            ->once()
            ->andReturn($genre);

        $this->resultItemFactory->shouldReceive('createAlbumListItem')
            ->with($album)
            ->once()
            ->andReturn($item);

        $album_list = function ($album): Generator {
            yield $album;
        };

        $this->albumRepository->shouldReceive('findByGenre')
            ->with($genre)
            ->once()
            ->andReturn($album_list($album));

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $item->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($data);

        $stream->shouldReceive('write')
            ->with(
                json_encode(['items' => [$data]], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['genreId' => (string) $genreId])
        );
    }
}
