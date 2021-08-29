<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Config;

use Psr\Log\LogLevel;

final class ConfigProvider implements ConfigProviderInterface
{
    public function getLogFilePath(): string
    {
        return $_ENV['LOG_PATH'] ?? '';
    }

    public function getJwtSecret(): string
    {
        return $_ENV['JWT_SECRET'] ?? '';
    }

    public function getCookieName(): string
    {
        return $_ENV['TOKEN_NAME'] ?? 'nekot';
    }

    public function getTokenLifetime(): int
    {
        return $_ENV['TOKEN_LIFETIME'] ?? 1086400;
    }

    public function getLogLevel(): string
    {
        return $_ENV['LOG_LEVEL'] ?? LogLevel::ERROR;
    }

    public function getCorsOrigin(): string
    {
        return $_ENV['CORS_ORIGIN'] ?? '';
    }

    public function getApiBasePath(): string
    {
        return $_ENV['API_BASE_PATH'] ?? '/';
    }

    public function getAssetPath(): string
    {
        return $_ENV['ASSET_PATH'] ?? '';
    }

    public function getBaseUrl(): string
    {
        $hostname = $_ENV['HOSTNAME'];
        $port = (int) $_ENV['PORT'];
        $ssl = $_ENV['SSL'] === true;

        $protocol = ($ssl === true)
            ? 'https'
            : 'http';

        $port_string = '';
        if (
            $port !== 0 &&
            !in_array($port, [80, 443], true)
        ) {
            $port_string = sprintf(':%d', $port);
        }

        return sprintf(
            '%s://%s%s/',
            $protocol,
            $hostname,
            $port_string
        );
    }
}
