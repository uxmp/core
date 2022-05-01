<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Session;

use Firebase\JWT\JWT;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

final class JwtManager implements JwtManagerInterface
{
    public function __construct(
        private readonly ConfigProviderInterface $config
    ) {
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->config->getJwtSecret());
    }
}
