<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;
use Uxmp\Core\Component\Authentication\SessionManagerInterface;

/**
 * Creates the api middleware classes
 */
final class MiddlewareFactory implements MiddlewareFactoryInterface
{
    public function __construct(
        private readonly SessionManagerInterface $sessionManager,
        private readonly Psr17Factory $psr17Factory,
    ) {
    }

    public function createRequestLoggingMiddleware(
        LoggerInterface $logger
    ): MiddlewareInterface {
        return new RequestLoggingMiddleware(
            $logger
        );
    }

    public function createSessionValidatorMiddleware(): MiddlewareInterface
    {
        return new SessionValidatorMiddleware(
            $this->sessionManager,
            $this->psr17Factory,
        );
    }
}
