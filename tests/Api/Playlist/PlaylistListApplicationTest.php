<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

class PlaylistListApplicationTest extends MockeryTestCase
{
    private MockInterface $playlistRepository;

    private PlaylistListApplication $subject;

    public function setUp(): void
    {
        $this->playlistRepository = Mockery::mock(PlaylistRepositoryInterface::class);

        $this->subject = new PlaylistListApplication(
            $this->playlistRepository
        );
    }

    public function testRunReturnsData(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $playlist = Mockery::mock(PlaylistInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $id = 666;
        $name = 'some-name';
        $userId = 42;
        $userName = 'some-username';
        $songCount = 33;

        $result = [[
            'id' => $id,
            'name' => $name,
            'song_count' => $songCount,
            'user_name' => $userName,
            'user_id' => $userId,
        ]];

        $this->playlistRepository->shouldReceive('findBy')
            ->with([], ['name' => 'ASC'])
            ->once()
            ->andReturn([$playlist]);

        $playlist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($id);
        $playlist->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn($name);
        $playlist->shouldReceive('getOwner')
            ->withNoArgs()
            ->once()
            ->andReturn($user);
        $playlist->shouldReceive('getSongCount')
            ->withNoArgs()
            ->once()
            ->andReturn($songCount);

        $user->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn($userName);
        $user->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($userId);

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
                json_encode(['items' => $result], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
