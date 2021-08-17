<?php

declare(strict_types=1);

namespace Usox\Core\Api\Public;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Usox\Core\Api\AbstractApiApplication;
use Usox\Core\Component\Config\ConfigProviderInterface;
use Usox\Core\Component\Session\SessionManagerInterface;

final class LogoutApplication extends AbstractApiApplication
{
    public function __construct(
        private SessionManagerInterface $sessionManager,
        private ConfigProviderInterface $configProvider
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $sessionId = $request->getAttribute('sessionId');
        if ($sessionId !== null) {
            $this->sessionManager->logout((int) $sessionId);
        }

        $response->getBody()->write(
            (string) json_encode(['items' => true], JSON_PRETTY_PRINT)
        );

        return $this
            ->asJson($response)
            ->withHeader(
                'Set-Cookie',
                sprintf(
                    '%s=; path=/; Expires=%s',
                    $this->configProvider->getCookieName(),
                    date(DATE_RFC1123)
                )
            )
            ;
    }
}
