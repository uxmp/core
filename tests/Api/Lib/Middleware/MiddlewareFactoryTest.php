<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Middleware;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Log\LoggerInterface;
use Uxmp\Core\Component\Authentication\SessionManagerInterface;

class MiddlewareFactoryTest extends MockeryTestCase
{
    private MockInterface $sessionManager;

    private MockInterface $psr17Factory;

    private MiddlewareFactory $subject;

    public function setUp(): void
    {
        $this->sessionManager = Mockery::mock(SessionManagerInterface::class);
        $this->psr17Factory = Mockery::mock(Psr17Factory::class);

        $this->subject = new MiddlewareFactory(
            $this->sessionManager,
            $this->psr17Factory,
        );
    }

    public function testCreateRequestLoggingMiddlewareReturnsInstance(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);

        $this->assertInstanceOf(
            RequestLoggingMiddleware::class,
            $this->subject->createRequestLoggingMiddleware($logger)
        );
    }

    public function testCreateSessionValidatorMiddlewareReturnsInstance(): void
    {
        $this->assertInstanceOf(
            SessionValidatorMiddleware::class,
            $this->subject->createSessionValidatorMiddleware()
        );
    }
}
