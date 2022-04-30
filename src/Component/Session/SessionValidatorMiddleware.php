<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Session;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Teapot\StatusCode;

class SessionValidatorMiddleware implements MiddlewareInterface
{
    public const SESSION_ID = 'sessionId';
    public const USER = 'user';
    public const USER_ID = 'userId';

    public function __construct(
        private SessionManagerInterface $sessionManager,
        private Psr17Factory $psr17Factory
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
