<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Common;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Session\SessionManagerInterface;

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

        return $this
            ->asJson($response, ['items' => true])
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
