<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\FavoriteInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\FavoriteRepositoryInterface;

class FavoriteListApplicationTest extends MockeryTestCase
{
    private Mockery\MockInterface $favoriteRepository;

    private FavoriteListApplication $subject;

    public function setUp(): void
    {
        $this->favoriteRepository = Mockery::mock(FavoriteRepositoryInterface::class);

        $this->subject = new FavoriteListApplication(
            $this->favoriteRepository
        );
    }

    public function testRunReturnsData(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $favorite = Mockery::mock(FavoriteInterface::class);
        $timestamp = new DateTime();

        $itemId = 666;
        $type = 'song';

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->favoriteRepository->shouldReceive('findBy')
            ->with(['user' => $user])
            ->once()
            ->andReturn([$favorite]);

        $result = [
            'album' => [],
            'song' => [$itemId => $timestamp->getTimestamp()],
            'artist' => [],
        ];

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode($result, JSON_PRETTY_PRINT))
            ->once();

        $favorite->shouldReceive('getType')
            ->withNoArgs()
            ->once()
            ->andReturn($type);
        $favorite->shouldReceive('getItemId')
            ->withNoArgs()
            ->once()
            ->andReturn($itemId);
        $favorite->shouldReceive('getDate')
            ->withNoArgs()
            ->once()
            ->andReturn($timestamp);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
