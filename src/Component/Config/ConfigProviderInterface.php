<?php

namespace Uxmp\Core\Component\Config;

use Psr\Log\LogLevel;

interface ConfigProviderInterface
{
    /**
     * Path to the log file storage folder
     */
    public function getLogFilePath(): string;

    /**
     * JWT secret which must be explicitly set by the user in the config
     */
    public function getJwtSecret(): string;

    /**
     * Name of the http cookie needed by the streaming routes
     */
    public function getCookieName(): string;

    /**
     * Max age of the jwt token
     */
    public function getTokenLifetime(): int;

    /**
     * Log level as defined by PSR
     *
     * @phpstan-return LogLevel::*
     */
    public function getLogLevel(): string;

    /**
     * Name of the CORS origin (e.g. if backend/frontend hostnames differ)
     */
    public function getCorsOrigin(): string;

    /**
     * Base path to the public folder
     */
    public function getApiBasePath(): string;

    /**
     * Path to the asset storage folder
     */
    public function getAssetPath(): string;

    /**
     * Debug mode setting
     */
    public function getDebugMode(): bool;

    /**
     * DSN for the database connection
     */
    public function getDatabaseDsn(): string;

    /**
     * Database password
     */
    public function getDatabasePassword(): string;

    /**
     * The base url for the usage in absolute urls
     */
    public function getBaseUrl(): string;

    /**
     * Max age of files in the clients' asset cache
     */
    public function getClientCacheMaxAge(): int;
}
