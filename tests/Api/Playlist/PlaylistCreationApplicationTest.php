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
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

class PlaylistCreationApplicationTest extends MockeryTestCase
{
    private MockInterface $playlistRepository;

    private MockInterface $schemaValidator;

    private PlaylistCreationApplication $subject;

    private array $handlerTypes = [42 => true];

    public function setUp(): void
    {
        $this->playlistRepository = Mockery::mock(PlaylistRepositoryInterface::class);
        $this->schemaValidator = Mockery::mock(SchemaValidatorInterface::class);

        $this->subject = new PlaylistCreationApplication(
            $this->playlistRepository,
            $this->schemaValidator,
            $this->handlerTypes,
        );
    }

    public function testRunErrorOnUnknownType(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $name = 'some-name';
        $typeId = 33;

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with(
                $request,
                'PlaylistCreation.json',
            )
            ->once()
            ->andReturn(['name' => $name, 'typeId' => $typeId,]);

        $response->shouldReceive('withStatus')
            ->with(StatusCode::BAD_REQUEST)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }

    public function testRunCreates(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $playlist = Mockery::mock(PlaylistInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $id = 666;
        $name = 'some-name';
        $typeId = 42;

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with(
                $request,
                'PlaylistCreation.json',
            )
            ->once()
            ->andReturn(['name' => $name, 'typeId' => $typeId,]);

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->playlistRepository->shouldReceive('prototype')
            ->withNoArgs()
            ->once()
            ->andReturn($playlist);
        $this->playlistRepository->shouldReceive('save')
            ->with($playlist)
            ->once();

        $playlist->shouldReceive('setName')
            ->with($name)
            ->once()
            ->andReturnSelf();
        $playlist->shouldReceive('setType')
            ->with($typeId)
            ->once()
            ->andReturnSelf();
        $playlist->shouldReceive('setOwner')
            ->with($user)
            ->once()
            ->andReturnSelf();
        $playlist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($id);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withStatus')
            ->with(StatusCode::CREATED)
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(
                json_encode(['result' => $id], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
