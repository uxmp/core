<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\RadioStation;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Model\RadioStationInterface;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;

class RadioStationCreationApplicationTest extends MockeryTestCase
{
    private MockInterface $radioStationRepository;

    private MockInterface $schemaValidator;

    private RadioStationCreationApplication $subject;

    public function setUp(): void
    {
        $this->radioStationRepository = Mockery::mock(RadioStationRepositoryInterface::class);
        $this->schemaValidator = Mockery::mock(SchemaValidatorInterface::class);

        $this->subject = new RadioStationCreationApplication(
            $this->radioStationRepository,
            $this->schemaValidator
        );
    }

    public function testRunCreates(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $station = Mockery::mock(RadioStationInterface::class);

        $id = 666;
        $name = 'some-name';
        $url = 'url';

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with(
                $request,
                'RadioStationCreation.json',
            )
            ->once()
            ->andReturn(['name' => $name, 'url' => $url]);

        $this->radioStationRepository->shouldReceive('prototype')
            ->withNoArgs()
            ->once()
            ->andReturn($station);
        $this->radioStationRepository->shouldReceive('save')
            ->with($station)
            ->once();

        $station->shouldReceive('setName')
            ->with($name)
            ->once()
            ->andReturnSelf();
        $station->shouldReceive('setUrl')
            ->with($url)
            ->once()
            ->andReturnSelf();
        $station->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($id);

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
                json_encode(['result' => $id], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
