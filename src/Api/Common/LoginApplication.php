<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Common;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Session\JwtManagerInterface;
use Uxmp\Core\Component\Session\SessionManagerInterface;

final class LoginApplication extends AbstractApiApplication
{
    public function __construct(
        private JwtManagerInterface $jwtManager,
        private ConfigProviderInterface $configProvider,
        private SessionManagerInterface $sessionManager
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        /** @var array<string, scalar> $body */
        $body = $request->getParsedBody();

        $username = (string) ($body['username'] ?? '');
        $password = (string) ($body['password'] ?? '');

        $session = $this->sessionManager->login($username, $password);

        if ($session === null) {
            return $this->asJson(
                $response,
                ['data' => ['msg' => 'Username or password wrong']]
            );
        }

        $lifetime = time() + $this->configProvider->getTokenLifetime();

        $payload = [
            'iat' => time(),
            'exp' => $lifetime,
            'sub' => (string) $session->getId(),
        ];

        $token = $this->jwtManager->encode($payload);
        $user = $session->getUser();

        return $this
            ->asJson(
                $response,
                [
                    'data' => [
                        'token' => $token,
                        'user' => ['id' => $user->getId(), 'name' => $user->getName()]
                    ]
                ]
            )
            ->withHeader(
                'Set-Cookie',
                sprintf(
                    '%s=%s; path=%s/play; Expires=%s',
                    $this->configProvider->getCookieName(),
                    $token,
                    $this->configProvider->getApiBasePath(),
                    date(DATE_RFC1123, $lifetime)
                )
            );
    }
}
