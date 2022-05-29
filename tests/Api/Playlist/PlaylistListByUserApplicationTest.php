<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use JsonSerializable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

class PlaylistListByUserApplicationTest extends MockeryTestCase
{
    private MockInterface $playlistRepository;

    private MockInterface $resultItemFactory;

    private PlaylistListByUserApplication $subject;

    public function setUp(): void
    {
        $this->playlistRepository = Mockery::mock(PlaylistRepositoryInterface::class);
        $this->resultItemFactory = Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new PlaylistListByUserApplication(
            $this->playlistRepository,
            $this->resultItemFactory
        );
    }

    public function testRunReturnsData(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $playlist = Mockery::mock(PlaylistInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $item = Mockery::mock(JsonSerializable::class);

        $result = ['some-result'];

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->playlistRepository->shouldReceive('findBy')
            ->with(['owner' => $user], ['name' => 'ASC'])
            ->once()
            ->andReturn([$playlist]);

        $this->resultItemFactory->shouldReceive('createPlaylistItem')
            ->with($playlist)
            ->once()
            ->andReturn($item);

        $item->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($result);

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
                json_encode(['items' => [$result]], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
