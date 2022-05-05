<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Common;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Component\Authentication\JwtManagerInterface;
use Uxmp\Core\Component\Authentication\SessionManagerInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

final class LoginApplication extends AbstractApiApplication
{
    /**
     * @param SchemaValidatorInterface<array{username: string, password: string}> $schemaValidator
     */
    public function __construct(
        private readonly JwtManagerInterface $jwtManager,
        private readonly ConfigProviderInterface $configProvider,
        private readonly SessionManagerInterface $sessionManager,
        private readonly SchemaValidatorInterface $schemaValidator,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'Login.json'
        );

        $session = $this->sessionManager->login(
            $body['username'],
            $body['password'],
        );

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
                        'user' => [
                            'id' => $user->getId(),
                            'name' => $user->getName(),
                            'language' => $user->getLanguage(),
                        ],
                    ],
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
