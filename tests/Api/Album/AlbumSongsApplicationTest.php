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
use Teapot\StatusCode;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class AlbumSongsApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $resultItemFactory;

    private AlbumSongsApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->resultItemFactory = Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new AlbumSongsApplication(
            $this->albumRepository,
            $this->resultItemFactory
        );
    }

    public function testRunReturnsNotFoundIfNotFound(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);

        $this->albumRepository->shouldReceive('find')
            ->with(0)
            ->once()
            ->andReturnNull();

        $response->shouldReceive('withStatus')
            ->with(StatusCode::NOT_FOUND)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }

    public function testRunReturnsAlbumData(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $disc = Mockery::mock(DiscInterface::class);
        $song = Mockery::mock(SongInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $songListItem = Mockery::mock(JsonSerializable::class);

        $albumId = 666;
        $discId = 84;
        $length = 123;
        $number = 42;
        $songResult = 'some-song-result';

        $this->resultItemFactory->shouldReceive('createSongListItem')
            ->with($song, $album)
            ->once()
            ->andReturn($songListItem);

        $songListItem->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($songResult);

        $this->albumRepository->shouldReceive('find')
            ->with($albumId)
            ->once()
            ->andReturn($album);

        $album->shouldReceive('getDiscs')
            ->withNoArgs()
            ->once()
            ->andReturn([$disc]);

        $disc->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($discId);
        $disc->shouldReceive('getSongs')
            ->withNoArgs()
            ->once()
            ->andReturn([$song]);
        $disc->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($length);
        $disc->shouldReceive('getNumber')
            ->withNoArgs()
            ->once()
            ->andReturn($number);

        $result = [
            'items' => [[
                'id' => $discId,
                'songs' => [$songResult],
                'length' => $length,
                'number' => $number,
            ]],
        ];

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode($result, JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                ['albumId' => (string) $albumId]
            )
        );
    }
}
