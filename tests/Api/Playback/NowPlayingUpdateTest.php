<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\TemporaryPlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepositoryInterface;

class NowPlayingUpdateTest extends MockeryTestCase
{
    private MockInterface $schemaValidator;

    private MockInterface $temporaryPlaylistRepository;

    private MockInterface $playbackHistoryRepository;

    private MockInterface $songRepository;

    private NowPlayingUpdate $subject;

    public function setUp(): void
    {
        $this->schemaValidator = Mockery::mock(SchemaValidatorInterface::class);
        $this->temporaryPlaylistRepository = Mockery::mock(TemporaryPlaylistRepositoryInterface::class);
        $this->playbackHistoryRepository = Mockery::mock(PlaybackHistoryRepositoryInterface::class);
        $this->songRepository = Mockery::mock(SongRepositoryInterface::class);

        $this->subject = new NowPlayingUpdate(
            $this->schemaValidator,
            $this->temporaryPlaylistRepository,
            $this->playbackHistoryRepository,
            $this->songRepository,
        );
    }

    public function testInvokeReturnsData(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $temporaryPlaylist = Mockery::mock(TemporaryPlaylistInterface::class);
        $song = Mockery::mock(SongInterface::class);
        $history = Mockery::mock(PlaybackHistoryInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $playlistId = 'some-id';
        $offset = 666;
        $songId = 42;

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->temporaryPlaylistRepository->shouldReceive('findOneBy')
            ->with([
                'id' => $playlistId,
                'owner' => $user,
            ])
            ->once()
            ->andReturn($temporaryPlaylist);

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with($request, 'NowPlayingUpdate.json')
            ->once()
            ->andReturn([
                'temporaryPlaylist' => [
                    'id' => $playlistId,
                    'offset' => $offset,
                ],
                'songId' => $songId,
            ]);
        $this->temporaryPlaylistRepository->shouldReceive('save')
            ->with($temporaryPlaylist)
            ->once();

        $temporaryPlaylist->shouldReceive('setOffset')
            ->with($offset)
            ->once();

        $this->songRepository->shouldReceive('find')
            ->with($songId)
            ->once()
            ->andReturn($song);

        $this->playbackHistoryRepository->shouldReceive('prototype->setUser')
            ->with($user)
            ->once()
            ->andReturn($history);
        $this->playbackHistoryRepository->shouldReceive('save')
            ->with($history)
            ->once();

        $history->shouldReceive('setSong')
            ->with($song)
            ->once()
            ->andReturnSelf();
        $history->shouldReceive('setPlayDate')
            ->with(Mockery::type(DateTime::class))
            ->once()
            ->andReturnSelf();

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
                'result' => true,
            ], JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
