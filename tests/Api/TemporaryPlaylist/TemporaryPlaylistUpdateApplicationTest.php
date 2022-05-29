<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\TemporaryPlaylist;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Model\TemporaryPlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepositoryInterface;

class TemporaryPlaylistUpdateApplicationTest extends MockeryTestCase
{
    private MockInterface $temporaryPlaylistRepository;

    private MockInterface $schemaValidator;

    private TemporaryPlaylistUpdateApplication $subject;

    public function setUp(): void
    {
        $this->temporaryPlaylistRepository = Mockery::mock(TemporaryPlaylistRepositoryInterface::class);
        $this->schemaValidator = Mockery::mock(SchemaValidatorInterface::class);

        $this->subject = new TemporaryPlaylistUpdateApplication(
            $this->temporaryPlaylistRepository,
            $this->schemaValidator,
        );
    }

    public function testRunReturnsResult(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $obj = Mockery::mock(TemporaryPlaylistInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $playlistId = 'some-playlist-id';
        $songId = 666;

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with($request, 'TemporaryPlaylistUpdate.json')
            ->once()
            ->andReturn(['songIds' => [$songId], 'playlistId' => $playlistId]);

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->temporaryPlaylistRepository->shouldReceive('findOneBy')
            ->with([
                'owner' => $user,
                'id' => $playlistId,
            ])
            ->once()
            ->andReturnNull();
        $this->temporaryPlaylistRepository->shouldReceive('prototype')
            ->withNoArgs()
            ->once()
            ->andReturn($obj);
        $this->temporaryPlaylistRepository->shouldReceive('save')
            ->with($obj)
            ->once();

        $obj->shouldReceive('setOwner')
            ->with($user)
            ->once()
            ->andReturnSelf();
        $obj->shouldReceive('setId')
            ->with($playlistId)
            ->once()
            ->andReturnSelf();
        $obj->shouldReceive('updateSongList')
            ->with([$songId])
            ->once();

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode(['result' => true], JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                []
            )
        );
    }
}
