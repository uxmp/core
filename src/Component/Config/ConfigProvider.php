<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Config;

use Configula\ConfigValues;
use Psr\Log\LogLevel;

final class ConfigProvider implements ConfigProviderInterface
{
    public function __construct(
        private readonly ConfigValues $configValues,
    ) {
    }

    public function getLogFilePath(): string
    {
        return $this->configValues->get('logging.path', '');
    }

    public function getJwtSecret(): string
    {
        return $this->configValues->get('security.jwt_secret', '');
    }

    public function getCookieName(): string
    {
        return $this->configValues->get('security.token_name', 'nekot');
    }

    public function getTokenLifetime(): int
    {
        return (int) $this->configValues->get('security.token_lifetime', 1_086_400);
    }

    public function getLogLevel(): string
    {
        return $this->configValues->get('logging.level', LogLevel::ERROR);
    }

    public function getCorsOrigin(): string
    {
        return $this->configValues->get('http.cors_origin', '');
    }

    public function getApiBasePath(): string
    {
        return $this->configValues->get('http.api_base_path', '');
    }

    public function getAssetPath(): string
    {
        return $this->configValues->get('assets.path', '');
    }

    public function getDebugMode(): bool
    {
        return (bool) $this->configValues->get('debug.enabled', false);
    }

    public function getDatabaseDsn(): string
    {
        return $this->configValues->get('database.dsn', '');
    }

    public function getDatabasePassword(): string
    {
        return $this->configValues->get('database.password', '');
    }

    public function getBaseUrl(): string
    {
        $hostname = $this->configValues->get('http.hostname', '');
        $port = (int) $this->configValues->get('http.port', 0);
        $ssl = ((bool) $this->configValues->get('http.ssl', true)) === true;

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
            '%s://%s%s%s',
            $protocol,
            $hostname,
            $port_string,
            $this->getApiBasePath()
        );
    }

    public function getClientCacheMaxAge(): int
    {
        return 86400 * 100;
    }
}
