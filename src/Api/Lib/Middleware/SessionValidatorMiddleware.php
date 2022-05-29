<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Teapot\StatusCode;
use Uxmp\Core\Component\Authentication\SessionManagerInterface;

/**
 * Provides authentication services for api requests
 */
final class SessionValidatorMiddleware implements MiddlewareInterface
{
    final public const SESSION_ID = 'sessionId';
    final public const USER = 'user';
    final public const USER_ID = 'userId';

    public function __construct(
        private readonly SessionManagerInterface $sessionManager,
        private readonly Psr17Factory $psr17Factory
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        /** @var null|array<scalar> $tokenData */
        $tokenData = $request->getAttribute('token');
        if ($tokenData !== null) {
            $session = $this->sessionManager->lookup((int) ($tokenData['sub'] ?? 0));

            if ($session === null || !$session->getActive()) {
                return $this->psr17Factory->createResponse(
                    StatusCode::FORBIDDEN,
                    'Session expired'
                );
            }

            $user = $session->getUser();

            $request = $request
                ->withAttribute(static::SESSION_ID, $session->getId())
                ->withAttribute(static::USER, $user)
                ->withAttribute(static::USER_ID, $user->getId())
            ;
        }

        return $handler->handle($request);
    }
}
