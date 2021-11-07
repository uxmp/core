<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;
use Uxmp\Core\Component\Favorite\FavoriteAbleInterface;
use Uxmp\Core\Component\Favorite\FavoriteManagerInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class FavoriteRemoveApplicationTest extends MockeryTestCase
{
    private MockInterface $songRepository;

    private MockInterface $favoriteManager;

    private FavoriteRemoveApplication $subject;

    public function setUp(): void
    {
        $this->songRepository = \Mockery::mock(SongRepositoryInterface::class);
        $this->favoriteManager = \Mockery::mock(FavoriteManagerInterface::class);

        $this->subject = new FavoriteRemoveApplication(
            $this->songRepository,
            $this->favoriteManager,
        );
    }

    public function testRunReturnsErrorResponseIfItemWasNotfound(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);

        $songId = 666;

        $this->songRepository->shouldReceive('find')
            ->with($songId)
            ->once()
            ->andReturnNull();

        $request->shouldReceive('getParsedBody')
            ->withNoArgs()
            ->once()
            ->andReturn([
                'itemId' => (string) $songId
            ]);

        $response->shouldReceive('withStatus')
            ->with(StatusCode::NOT_FOUND)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                [
                    'type' => 'song'
                ]
            )
        );
    }

    /**
     * @dataProvider itemDataProvider
     */
    public function testRunReturnsManagerResult(
        string $itemType,
        string $repositoryName,
    ): void {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $obj = \Mockery::mock(FavoriteAbleInterface::class);
        $user = \Mockery::mock(UserInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);

        $songId = 666;
        $userId = 42;

        $this->{$repositoryName}->shouldReceive('find')
            ->with($songId)
            ->once()
            ->andReturn($obj);

        $request->shouldReceive('getParsedBody')
            ->withNoArgs()
            ->once()
            ->andReturn([
                'itemId' => (string) $songId
            ]);
        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $user->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($userId);

        $this->favoriteManager->shouldReceive('remove')
            ->with($obj, $userId)
            ->once()
            ->andReturnFalse();

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode(['result' => false], JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                [
                    'type' => $itemType
                ]
            )
        );
    }

    public function itemDataProvider(): array
    {
        return [
            ['song', 'songRepository'],
        ];
    }
}
