<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Common;

use InvalidArgumentException;
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
        /** @var array<string, mixed> $body */
        $body = $request->getParsedBody();

        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';

        $session = $this->sessionManager->login($username, $password);

        if ($session === null) {
            throw new InvalidArgumentException();
        }

        $lifetime = time() + $this->configProvider->getTokenLifetime();

        $payload = [
            'iat' => time(),
            'exp' => $lifetime,
            'sub' => (string) $session->getId(),
        ];

        $token = $this->jwtManager->encode($payload);

        return $this
            ->asJson($response, ['items' => ['token' => $token]])
            ->withHeader(
                'Set-Cookie',
                sprintf(
                    '%s=%s; path=/; Expires=%s',
                    $this->configProvider->getCookieName(),
                    $token,
                    date(DATE_RFC1123, $lifetime)
                )
            );
    }
}
