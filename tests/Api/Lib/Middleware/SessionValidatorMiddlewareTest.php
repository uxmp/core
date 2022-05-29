<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Middleware;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Teapot\StatusCode;
use Uxmp\Core\Component\Authentication\SessionManagerInterface;
use Uxmp\Core\Orm\Model\SessionInterface;
use Uxmp\Core\Orm\Model\UserInterface;

class SessionValidatorMiddlewareTest extends MockeryTestCase
{
    private MockInterface $sessionManager;

    private MockInterface $psr17Factory;

    private SessionValidatorMiddleware $subject;

    public function setUp(): void
    {
        $this->sessionManager = Mockery::mock(SessionManagerInterface::class);
        $this->psr17Factory = Mockery::mock(Psr17Factory::class);

        $this->subject = new SessionValidatorMiddleware(
            $this->sessionManager,
            $this->psr17Factory
        );
    }

    public function testProcessJustHandlesNextHandler(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $request->shouldReceive('getAttribute')
            ->with('token')
            ->once()
            ->andReturnNull();

        $handler->shouldReceive('handle')
            ->with($request)
            ->once()
            ->andReturn($response);

        $this->assertSame(
            $response,
            $this->subject->process($request, $handler)
        );
    }

    public function testProcessReturnsForbiddenResponse(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $request->shouldReceive('getAttribute')
            ->with('token')
            ->once()
            ->andReturn([]);

        $this->sessionManager->shouldReceive('lookup')
            ->with(0)
            ->once()
            ->andReturnNull();

        $this->psr17Factory->shouldReceive('createResponse')
            ->with(StatusCode::FORBIDDEN, 'Session expired')
            ->once()
            ->andReturn($response);

        $this->assertSame(
            $response,
            $this->subject->process($request, $handler)
        );
    }

    public function testProcessReturnsForbiddenResponseIfSessionIsNotActive(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $session = Mockery::mock(SessionInterface::class);

        $request->shouldReceive('getAttribute')
            ->with('token')
            ->once()
            ->andReturn([]);

        $this->sessionManager->shouldReceive('lookup')
            ->with(0)
            ->once()
            ->andReturn($session);

        $session->shouldReceive('getActive')
            ->withNoArgs()
            ->once()
            ->andReturnFalse();

        $this->psr17Factory->shouldReceive('createResponse')
            ->with(StatusCode::FORBIDDEN, 'Session expired')
            ->once()
            ->andReturn($response);

        $this->assertSame(
            $response,
            $this->subject->process($request, $handler)
        );
    }

    public function testProcessEnrichesRequestWithSessionDate(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $session = Mockery::mock(SessionInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $sessionId = 666;
        $userId = 42;

        $user->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($userId);

        $session->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($sessionId);
        $session->shouldReceive('getUser')
            ->withNoArgs()
            ->once()
            ->andReturn($user);
        $session->shouldReceive('getActive')
            ->withNoArgs()
            ->once()
            ->andReturnTrue();

        $request->shouldReceive('getAttribute')
            ->with('token')
            ->once()
            ->andReturn(['sub' => (string) $sessionId]);
        $request->shouldReceive('withAttribute')
            ->with('sessionId', $sessionId)
            ->once()
            ->andReturnSelf();
        $request->shouldReceive('withAttribute')
            ->with('user', $user)
            ->once()
            ->andReturnSelf();
        $request->shouldReceive('withAttribute')
            ->with('userId', $userId)
            ->once()
            ->andReturnSelf();

        $this->sessionManager->shouldReceive('lookup')
            ->with($sessionId)
            ->once()
            ->andReturn($session);

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
