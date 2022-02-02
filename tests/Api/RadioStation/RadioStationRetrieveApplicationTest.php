<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\RadioStation;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;
use Uxmp\Core\Orm\Model\RadioStationInterface;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;

class RadioStationRetrieveApplicationTest extends MockeryTestCase
{
    private MockInterface $radioStationRepository;

    private RadioStationRetrieveApplication $subject;

    public function setUp(): void
    {
        $this->radioStationRepository = Mockery::mock(RadioStationRepositoryInterface::class);

        $this->subject = new RadioStationRetrieveApplication(
            $this->radioStationRepository,
        );
    }

    public function testRunReturnsNotFoundIfStatioWasNotFound(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $this->radioStationRepository->shouldReceive('find')
            ->with(0)
            ->once()
            ->andReturnNull();

        $response->shouldReceive('withStatus')
            ->with(StatusCode::NOT_FOUND)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }

    public function testRunReturnsStation(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $station = Mockery::mock(RadioStationInterface::class);

        $stationId = 666;
        $name = 'some-name';
        $url = 'some-url';

        $result = [
            'id' => $stationId,
            'name' => $name,
            'url' => $url,
        ];

        $station->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($stationId);
        $station->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn($name);
        $station->shouldReceive('getUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($url);

        $this->radioStationRepository->shouldReceive('find')
            ->with($stationId)
            ->once()
            ->andReturn($station);

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
                json_encode($result, JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['stationId' => (string) $stationId])
        );
    }
}
