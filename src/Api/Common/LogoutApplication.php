<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Common;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Session\SessionManagerInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;

final class LogoutApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly SessionManagerInterface $sessionManager,
        private readonly ConfigProviderInterface $configProvider
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        /** @var string|null $sessionId */
        $sessionId = $request->getAttribute(SessionValidatorMiddleware::SESSION_ID);
        if ($sessionId !== null) {
            $this->sessionManager->logout((int) $sessionId);
        }

        return $this
            ->asJson($response, ['items' => true])
            ->withHeader(
                'Set-Cookie',
                sprintf(
                    '%s=; path=%s/play; Expires=%s',
                    $this->configProvider->getCookieName(),
                    $this->configProvider->getApiBasePath(),
                    date(DATE_RFC1123)
                )
            )
            ;
    }
}
