<?php

namespace Uxmp\Core\Component\Config;

interface ConfigProviderInterface
{
    public function getLogFilePath(): string;

    public function getJwtSecret(): string;

    public function getCookieName(): string;

    public function getTokenLifetime(): int;

    public function getLogLevel(): int;

    public function getDbDsn(): string;

    public function getDbPassword(): string;
}