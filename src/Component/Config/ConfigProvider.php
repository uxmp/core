<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Config;

use Configula\ConfigValues;
use Psr\Log\LogLevel;

/**
 * Provides typed access to config values
 */
final class ConfigProvider implements ConfigProviderInterface
{
    private const PROTO_HTTP = 'http';
    private const PROTO_HTTPS = 'https';

    private const HTTP_PORT = 80;
    private const HTTPS_PORT = 443;

    // Default values (ten days)
    private const DEFAULT_TOKEN_LIFETIME = 1_086_400;

    public function __construct(
        private readonly ConfigValues $configValues,
    ) {
    }

    /**
     * Path to the log file storage folder
     */
    public function getLogFilePath(): string
    {
        return $this->configValues->get('logging.path', '');
    }

    /**
     * JWT secret which must be explicitly set by the user in the config
     */
    public function getJwtSecret(): string
    {
        return $this->configValues->get('security.jwt_secret', '');
    }

    /**
     * Name of the http cookie needed by the streaming routes
     */
    public function getCookieName(): string
    {
        return $this->configValues->get('security.token_name', 'nekot');
    }

    /**
     * Max age of the jwt token
     */
    public function getTokenLifetime(): int
    {
        return (int) $this->configValues->get(
            'security.token_lifetime',
            self::DEFAULT_TOKEN_LIFETIME
        );
    }

    /**
     * Log level as defined by PSR
     *
     * @phpstan-return LogLevel::*
     */
    public function getLogLevel(): string
    {
        return $this->configValues->get('logging.level', LogLevel::ERROR);
    }

    /**
     * Name of the CORS origin (e.g. if backend/frontend hostnames differ)
     */
    public function getCorsOrigin(): string
    {
        return $this->configValues->get('http.cors_origin', '');
    }

    /**
     * Base path to the public folder
     */
    public function getApiBasePath(): string
    {
        return $this->configValues->get('http.api_base_path', '');
    }

    /**
     * Path to the asset storage folder
     */
    public function getAssetPath(): string
    {
        return $this->configValues->get('assets.path', '');
    }

    /**
     * Debug mode setting
     */
    public function getDebugMode(): bool
    {
        return (bool) $this->configValues->get('debug.enabled', false);
    }

    /**
     * DSN for the database connection
     */
    public function getDatabaseDsn(): string
    {
        return $this->configValues->get('database.dsn', '');
    }

    /**
     * Database password
     */
    public function getDatabasePassword(): string
    {
        return $this->configValues->get('database.password', '');
    }

    /**
     * The base url for the usage in absolute urls
     */
    public function getBaseUrl(): string
    {
        $hostname = $this->configValues->get('http.hostname', '');
        $port = (int) $this->configValues->get('http.port', 0);
        $ssl = ((bool) $this->configValues->get('http.ssl', true)) === true;

        $protocol = ($ssl === true)
            ? self::PROTO_HTTPS
            : self::PROTO_HTTP;

        $port_string = '';
        if (
            $port !== 0 &&
            !in_array($port, [self::HTTP_PORT, self::HTTPS_PORT], true)
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

    /**
     * Max age of files in the clients' asset cache
     */
    public function getClientCacheMaxAge(): int
    {
        return 86400 * 100;
    }
}
