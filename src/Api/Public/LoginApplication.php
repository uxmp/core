<?php

declare(strict_types=1);

namespace Usox\Core\Api\Public;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Usox\Core\Api\AbstractApiApplication;
use Usox\Core\Component\Config\ConfigProviderInterface;
use Usox\Core\Component\Session\JwtManagerInterface;
use Usox\Core\Component\Session\SessionManagerInterface;

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
        $body = $request->getParsedBody();

        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';

        $session = $this->sessionManager->login($username, $password);

        if ($session === null) {
            throw new \InvalidArgumentException();
        }

        $payload = [
            'iat' => time(),
            'exp' => time() + $this->configProvider->getTokenLifetime(),
            'sub' => (string) $session->getId(),
        ];

        $token = $this->jwtManager->encode($payload);

        $response->getBody()->write(
            (string) json_encode(['items' => ['token' => $token]], JSON_PRETTY_PRINT)
        );

        return $this
            ->asJson($response)
            ->withHeader(
                'Set-Cookie',
                sprintf(
                    '%s=%s; path=/; Expires=%s',
                    $this->configProvider->getCookieName(),
                    $token,
                    date(DATE_RFC1123, time() + $this->configProvider->getTokenLifetime())
                )
            );
    }
}
