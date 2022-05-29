<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;

interface MiddlewareFactoryInterface
{
    public function createRequestLoggingMiddleware(LoggerInterface $logger): MiddlewareInterface;

    public function createSessionValidatorMiddleware(): MiddlewareInterface;
}
