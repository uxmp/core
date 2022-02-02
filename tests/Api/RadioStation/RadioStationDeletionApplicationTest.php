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

class RadioStationDeletionApplicationTest extends MockeryTestCase
{
    private MockInterface $radioStationRepository;

    private RadioStationDeletionApplication $subject;

    public function setUp(): void
    {
        $this->radioStationRepository = Mockery::mock(RadioStationRepositoryInterface::class);

        $this->subject = new RadioStationDeletionApplication(
            $this->radioStationRepository,
        );
    }

    public function testRunDeletes(): void
    {
        $station = Mockery::mock(RadioStationInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $stationId = 666;

        $this->radioStationRepository->shouldReceive('find')
            ->with($stationId)
            ->once()
            ->andReturn($station);
        $this->radioStationRepository->shouldReceive('delete')
            ->with($station)
            ->once();

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
                json_encode(['result' => true], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['stationId' => (string) $stationId])
        );
    }

    public function testRunDoesNotDeleteIfItemWasNotFound(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $stationId = 666;

        $this->radioStationRepository->shouldReceive('find')
            ->with($stationId)
            ->once()
            ->andReturnNull();

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
                json_encode(['result' => false], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['stationId' => (string) $stationId])
        );
    }
}
