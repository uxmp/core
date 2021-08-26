<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Config;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Log\LogLevel;

class ConfigProviderTest extends MockeryTestCase
{
    private ConfigProvider $subject;

    public function setUp(): void
    {
        $this->subject = new ConfigProvider();
    }

    /**
     * @dataProvider valueDataProvider
     */
    public function testGetterReturnsValueOrDefault(
        string $getter,
        string $variable,
        mixed $default,
        mixed $value
    ): void {
        $this->assertSame(
            $default,
            call_user_func([$this->subject, $getter])
        );

        $_ENV[$variable] = $value;

        $this->assertSame(
            $value,
            call_user_func([$this->subject, $getter])
        );
    }

    public function valueDataProvider(): array
    {
        return [
            ['getLogFilePath', 'LOG_PATH', '', 'some-path'],
            ['getJwtSecret', 'JWT_SECRET', '', 'some-jwt'],
            ['getCookieName', 'TOKEN_NAME', 'nekot', 'some-token'],
            ['getTokenLifetime', 'TOKEN_LIFETIME', 1086400, 666],
            ['getLogLevel', 'LOG_LEVEL', LogLevel::ERROR, 'debug'],
            ['getCorsOrigin', 'CORS_ORIGIN', '', 'some-cors-origin'],
        ];
    }
}
