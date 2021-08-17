<?php

declare(strict_types=1);

namespace Usox\Core\Component\Config;

use Monolog\Logger;

final class ConfigProvider implements ConfigProviderInterface
{
    public function getLogFilePath(): string
    {
        return __DIR__ . '/../../../../logs/';
    }

    public function getJwtSecret(): string
    {
        return 'todochangeme';
    }

    public function getCookieName(): string
    {
        return 'nekot';
    }

    public function getTokenLifetime(): int
    {
        return 1086400;
    }

    public function getLogLevel(): int
    {
        return Logger::DEBUG;
    }

    public function getDbDsn(): string
    {
        return 'sqlite:///'. __DIR__ .'/../../../../dev/db.sqlite';
    }

    public function getDbPassword(): string
    {
        return '';
    }
}