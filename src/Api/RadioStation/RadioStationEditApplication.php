<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\RadioStation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;

/**
 * Edits a radio station
 */
final class RadioStationEditApplication extends AbstractApiApplication
{
    /**
     * @param SchemaValidatorInterface<array{name: string, url: string}> $schemaValidator
     */
    public function __construct(
        private readonly RadioStationRepositoryInterface $radioStationRepository,
        private readonly SchemaValidatorInterface $schemaValidator,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $stationId = (int) ($args['stationId'] ?? 0);

        $station = $this->radioStationRepository->find($stationId);
        if ($station === null) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'RadioStationCreation.json',
        );

        $station
            ->setUrl($body['url'])
            ->setName($body['name']);

        $this->radioStationRepository->save($station);

        return $this->asJson(
            $response,
            [
                'result' => true,
            ]
        );
    }
}
