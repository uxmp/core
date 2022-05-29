<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

class PlaylistEditApplicationTest extends MockeryTestCase
{
    private MockInterface $playlistRepository;

    private MockInterface $schemaValidator;

    private PlaylistEditApplication $subject;

    public function setUp(): void
    {
        $this->playlistRepository = Mockery::mock(PlaylistRepositoryInterface::class);
        $this->schemaValidator = Mockery::mock(SchemaValidatorInterface::class);

        $this->subject = new PlaylistEditApplication(
            $this->playlistRepository,
            $this->schemaValidator
        );
    }

    public function testRunErrorsIfPlaylistWasNotFound(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $this->playlistRepository->shouldReceive('find')
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

    public function testRunEdits(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $playlist = Mockery::mock(PlaylistInterface::class);

        $id = 666;
        $name = 'some-name';
        $userId = 42;

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER_ID)
            ->once()
            ->andReturn($userId);

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with(
                $request,
                'PlaylistCreation.json',
            )
            ->once()
            ->andReturn(['name' => $name]);

        $this->playlistRepository->shouldReceive('find')
            ->with($id)
            ->once()
            ->andReturn($playlist);
        $this->playlistRepository->shouldReceive('save')
            ->with($playlist)
            ->once();

        $playlist->shouldReceive('setName')
            ->with($name)
            ->once()
            ->andReturnSelf();
        $playlist->shouldReceive('getOwner->getId')
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
                json_encode(['result' => true], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['playlistId' => (string) $id])
        );
    }
}
