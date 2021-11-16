<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use JsonSerializable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;

class PlaybackHistoryApplicationTest extends MockeryTestCase
{
    private MockInterface $playbackHistoryRepository;

    private MockInterface $resultItemFactory;

    private PlaybackHistoryApplication $subject;

    public function setUp(): void
    {
        $this->playbackHistoryRepository = Mockery::mock(PlaybackHistoryRepositoryInterface::class);
        $this->resultItemFactory = Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new PlaybackHistoryApplication(
            $this->playbackHistoryRepository,
            $this->resultItemFactory,
        );
    }

    public function testRunReturnsData(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $historyItem = Mockery::mock(PlaybackHistoryInterface::class);
        $resultItem = Mockery::mock(JsonSerializable::class);
        $stream = Mockery::mock(StreamInterface::class);

        $resultData = ['some-result'];

        $this->resultItemFactory->shouldReceive('createPlaybackHistoryItem')
            ->with($historyItem)
            ->once()
            ->andReturn($resultItem);

        $this->playbackHistoryRepository->shouldReceive('findBy')
            ->with([], ['play_date' => 'DESC'], 15)
            ->once()
            ->andReturn([$historyItem]);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(json_encode(['items' => [$resultData]], JSON_PRETTY_PRINT))
            ->once();

        $resultItem->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($resultData);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
