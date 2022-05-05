<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Authentication;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

class JwtManagerTest extends MockeryTestCase
{
    private MockInterface $config;

    private JwtManager $subject;

    public function setUp(): void
    {
        $this->config = \Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new JwtManager(
            $this->config
        );
    }

    public function testEncodeReturnsData(): void
    {
        $secret = 'some-secret';
        $payload = ['some' => 'payload'];

        $this->config->shouldReceive('getJwtSecret')
            ->withNoArgs()
            ->once()
            ->andReturn($secret);

        $this->assertSame(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzb21lIjoicGF5bG9hZCJ9.WHsGHKBwd7-jFFVw5ELGpo-uWf2NCSFY8Jz8RDWfsjk',
            $this->subject->encode($payload)
        );
    }
}
