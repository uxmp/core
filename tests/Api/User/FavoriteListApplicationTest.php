<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class FavoriteListApplicationTest extends MockeryTestCase
{
    private FavoriteListApplication $subject;

    public function setUp(): void
    {
        $this->subject = new FavoriteListApplication();
    }

    public function testRunReturnsData(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);

        $result = [
            'albums' => [],
            'songs' => [],
            'artists' => [],
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

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
