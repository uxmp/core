<?php

declare(strict_types=1);

namespace Usox\Core\Component\Session;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Teapot\StatusCode;

final class SessionValidatorMiddleware implements MiddlewareInterface
{
    public function __construct(
        private SessionManagerInterface $sessionManager,
        private Psr17Factory $psr17Factory
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $tokenData = $request->getAttribute('token');
        if ($tokenData !== null) {
            $session = $this->sessionManager->lookup($tokenData['sub'] ?? '');

            if ($session === null) {
                return $this->psr17Factory->createResponse(
                    StatusCode::FORBIDDEN,
                    'Session expired'
                );
            }

            $request = $request
                ->withAttribute('sessionId', $session->getId())
                ->withAttribute('user', $session->getUser())
            ;
        }

        return $handler->handle($request);
    }
}
