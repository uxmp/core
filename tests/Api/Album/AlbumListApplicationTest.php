<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use JsonSerializable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class AlbumListApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $resultItemFactory;

    private AlbumListApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->resultItemFactory = Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new AlbumListApplication(
            $this->albumRepository,
            $this->resultItemFactory,
        );
    }

    public function testRunReturnsList(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $item = Mockery::mock(JsonSerializable::class);

        $data = ['some-data'];

        $this->resultItemFactory->shouldReceive('createAlbumListItem')
            ->with($album)
            ->once()
            ->andReturn($item);

        $this->albumRepository->shouldReceive('findBy')
            ->with([], ['title' => 'ASC'])
            ->once()
            ->andReturn([$album]);

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
            call_user_func($this->subject, $request, $response, [])
        );
    }

    public function testRunReturnsListWithCertainArtist(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $item = Mockery::mock(JsonSerializable::class);

        $artistId = 42;
        $data = ['some-data'];

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
                json_encode(['items' => [$data]], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->resultItemFactory->shouldReceive('createAlbumListItem')
            ->with($album)
            ->once()
            ->andReturn($item);

        $this->albumRepository->shouldReceive('findBy')
            ->with(['artist_id' => $artistId], ['title' => 'ASC'])
            ->once()
            ->andReturn([$album]);

        $item->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($data);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['artistId' => (string) $artistId])
        );
    }
}
