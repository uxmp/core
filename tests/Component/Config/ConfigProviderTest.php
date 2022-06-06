<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Config;

use Configula\ConfigValues;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Log\LogLevel;

class ConfigProviderTest extends MockeryTestCase
{
    private MockInterface $configValues;

    private ConfigProvider $subject;

    public function setUp(): void
    {
        $this->configValues = Mockery::mock(ConfigValues::class);

        $this->subject = new ConfigProvider(
            $this->configValues,
        );
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
        $this->configValues->shouldReceive('get')
            ->with($variable, $default)
            ->once()
            ->andReturn($value);

        $this->assertSame(
            $value,
            call_user_func([$this->subject, $getter])
        );
    }

    public function valueDataProvider(): array
    {
        return [
            ['getLogFilePath', 'logging.path', '', 'some-path'],
            ['getJwtSecret', 'security.jwt_secret', '', 'some-jwt'],
            ['getCookieName', 'security.token_name', 'nekot', 'some-token'],
            ['getTokenLifetime', 'security.token_lifetime', 1_086_400, 666],
            ['getLogLevel', 'logging.level', LogLevel::ERROR, 'debug'],
            ['getCorsOrigin', 'http.cors_origin', '', 'some-cors-origin'],
            ['getApiBasePath', 'http.api_base_path', '', 'some-base-path'],
            ['getAssetPath', 'assets.path', '', 'some-asset-path'],
            ['getDebugMode', 'debug.enabled', false, true],
            ['getDatabaseDsn', 'database.dsn', '', 'snafu'],
            ['getDatabasePassword', 'database.password', '', 'snafu'],
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
        $this->configValues->shouldReceive('get')
            ->with('http.hostname', '')
            ->once()
            ->andReturn($hostName);
        $this->configValues->shouldReceive('get')
            ->with('http.port', 0)
            ->once()
            ->andReturn((string) $port);
        $this->configValues->shouldReceive('get')
            ->with('http.ssl', true)
            ->once()
            ->andReturn($ssl);
        $this->configValues->shouldReceive('get')
            ->with('http.api_base_path', '')
            ->once()
            ->andReturn($basePath);

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
