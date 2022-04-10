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
            ['getApiBasePath', 'API_BASE_PATH', '', 'some-base-path'],
            ['getAssetPath', 'ASSET_PATH', '', 'some-asset-path'],
            ['getDebugMode', 'DEBUG_MODE', false, false],
        ];
    }

    /**
     * @dataProvider baseUrlDataProvider
     */
    public function testGetBaseUrlReturnsUrl(
        string $basePath,
        string $hostName,
        int $port,
        int $ssl,
        string $url
    ): void {
        $_ENV['HOSTNAME'] = $hostName;
        $_ENV['PORT'] = (string) $port;
        $_ENV['SSL'] = $ssl;
        $_ENV['API_BASE_PATH'] = $basePath;

        $this->assertSame(
            $url,
            $this->subject->getBaseUrl()
        );
    }

    public function baseUrlDataProvider(): array
    {
        return [
            [
                '/some-base-path',
                'some-host-name',
                80,
                1,
                'https://some-host-name/some-base-path',
            ],
            [
                '',
                'some-host-name',
                666,
                0,
                'http://some-host-name:666',
            ],
            [
                '/api',
                'some-host-name',
                443,
                1,
                'https://some-host-name/api',
            ],
        ];
    }

    public function testGetClientCacheMaxAgeReturnsValue(): void
    {
        $this->assertSame(
            100 * 86400,
            $this->subject->getClientCacheMaxAge()
        );
    }
}
