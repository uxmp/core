<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\RadioStation;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Orm\Model\RadioStationInterface;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;

class RadioStationListApplicationTest extends MockeryTestCase
{
    private MockInterface $radioStationRepository;

    private RadioStationListApplication $subject;

    public function setUp(): void
    {
        $this->radioStationRepository = Mockery::mock(RadioStationRepositoryInterface::class);

        $this->subject = new RadioStationListApplication(
            $this->radioStationRepository
        );
    }

    public function testRunReturnsData(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $station = Mockery::mock(RadioStationInterface::class);

        $id = 666;
        $name = 'some-name';
        $url = 'some-url';

        $result = [[
            'id' => $id,
            'name' => $name,
            'url' => $url,
        ]];

        $this->radioStationRepository->shouldReceive('findBy')
            ->with([], ['name' => 'ASC'])
            ->once()
            ->andReturn([$station]);

        $station->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($id);
        $station->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn($name);
        $station->shouldReceive('getUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($url);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(
                json_encode(['items' => $result], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
