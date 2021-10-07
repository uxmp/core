<?php

namespace Uxmp\Core\Component\Config;

use Psr\Log\LogLevel;

interface ConfigProviderInterface
{
    public function getLogFilePath(): string;

    public function getJwtSecret(): string;

    public function getCookieName(): string;

    public function getTokenLifetime(): int;

    /**
     * @phpstan-return LogLevel::*
     */
    public function getLogLevel(): string;

    public function getCorsOrigin(): string;

    public function getApiBasePath(): string;

    public function getAssetPath(): string;

    public function getBaseUrl(): string;

    public function getClientCacheMaxAge(): int;
}
