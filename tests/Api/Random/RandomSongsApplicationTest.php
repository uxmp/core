<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Random;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Api\Lib\SongListItemInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class RandomSongsApplicationTest extends MockeryTestCase
{
    private MockInterface $songRepository;

    private MockInterface $resultItemFactory;

    private RandomSongsApplication $subject;

    public function setUp(): void
    {
        $this->songRepository = \Mockery::mock(SongRepositoryInterface::class);
        $this->resultItemFactory = \Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new RandomSongsApplication(
            $this->songRepository,
            $this->resultItemFactory,
        );
    }

    public function testRunReturnsList(): void
    {
        $song = \Mockery::mock(SongInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $request = \Mockery::mock(ServerRequestInterface::class);
        $album = \Mockery::mock(AlbumInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);
        $item = \Mockery::mock(SongListItemInterface::class);

        $result = 'some-result';

        $this->songRepository->shouldReceive('findAll')
            ->withNoArgs()
            ->once()
            ->andReturn([$song]);

        $this->resultItemFactory->shouldReceive('createSongListItem')
            ->with($song, $album)
            ->once()
            ->andReturn($item);

        $item->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn([$result]);

        $song->shouldReceive('getDisc->getAlbum')
            ->withNoArgs()
            ->once()
            ->andReturn($album);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(json_encode([
                'items' => [[
                    $result
                ]]
            ], JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['limit' => 1])
        );
    }
}
