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
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Component\Playlist\MediaAddition\PlaylistMediaAdderInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

class PlaylistAddMediaApplicationTest extends MockeryTestCase
{
    private MockInterface $playlistRepository;

    private MockInterface $schemaValidator;

    private MockInterface $playlistMediaAdder;

    private PlaylistAddMediaApplication $subject;

    public function setUp(): void
    {
        $this->playlistRepository = Mockery::mock(PlaylistRepositoryInterface::class);
        $this->schemaValidator = Mockery::mock(SchemaValidatorInterface::class);
        $this->playlistMediaAdder = Mockery::mock(PlaylistMediaAdderInterface::class);

        $this->subject = new PlaylistAddMediaApplication(
            $this->playlistRepository,
            $this->schemaValidator,
            $this->playlistMediaAdder,
        );
    }

    public function testRunAddsMedia(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $playlist = Mockery::mock(PlaylistInterface::class);

        $playlistId = 666;
        $mediaType = 'some-type';
        $mediaId = 42;
        $userId = 33;

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with(
                $request,
                'PlaylistMediaAddition.json',
            )
            ->once()
            ->andReturn(['mediaType' => $mediaType, 'mediaId' => $mediaId,]);

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER_ID)
            ->once()
            ->andReturn($userId);

        $this->playlistRepository->shouldReceive('find')
            ->with($playlistId)
            ->once()
            ->andReturn($playlist);

        $this->playlistMediaAdder->shouldReceive('add')
            ->with($playlist, $mediaType, $mediaId)
            ->once();

        $playlist->shouldReceive('isStatic')
            ->withNoArgs()
            ->once()
            ->andReturnTrue();
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
            call_user_func($this->subject, $request, $response, ['playlistId' => (string) $playlistId])
        );
    }

    public function testRunErrorsIfPlaylistWasNotFound(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $playlistId = 666;

        $this->playlistRepository->shouldReceive('find')
            ->with($playlistId)
            ->once()
            ->andReturnNull();

        $response->shouldReceive('withStatus')
            ->with(StatusCode::NOT_FOUND)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['playlistId' => (string) $playlistId])
        );
    }

    public function testRunErrorsIfPlaylistIsNotStatic(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $playlist = Mockery::mock(PlaylistInterface::class);

        $playlistId = 666;

        $this->playlistRepository->shouldReceive('find')
            ->with($playlistId)
            ->once()
            ->andReturn($playlist);

        $response->shouldReceive('withStatus')
            ->with(StatusCode::NOT_FOUND)
            ->once()
            ->andReturnSelf();

        $playlist->shouldReceive('isStatic')
            ->withNoArgs()
            ->once()
            ->andReturnFalse();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['playlistId' => (string) $playlistId])
        );
    }
}
