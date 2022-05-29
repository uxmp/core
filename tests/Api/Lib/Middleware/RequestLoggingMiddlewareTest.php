<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Middleware;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class RequestLoggingMiddlewareTest extends MockeryTestCase
{
    private MockInterface $logger;

    private RequestLoggingMiddleware $subject;

    public function setUp(): void
    {
        $this->logger = Mockery::mock(LoggerInterface::class);

        $this->subject = new RequestLoggingMiddleware(
            $this->logger,
        );
    }

    public function testProcessLogs(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $uri = Mockery::mock(UriInterface::class);

        $uriValue = 'some-uri';

        $request->shouldReceive('getUri')
            ->withNoArgs()
            ->once()
            ->andReturn($uri);

        $uri->shouldReceive('__toString')
            ->withNoArgs()
            ->once()
            ->andReturn($uriValue);

        $this->logger->shouldReceive('info')
            ->with($uriValue)
            ->once();

        $handler->shouldReceive('handle')
            ->with($request)
            ->once()
            ->andReturn($response);

        $this->assertSame(
            $response,
            $this->subject->process($request, $handler)
        );
    }
}
