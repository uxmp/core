<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\RadioStation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;

/**
 * Retrieves a radio station
 */
final class RadioStationRetrieveApplication extends AbstractApiApplication
{
    public function __construct(
        private RadioStationRepositoryInterface $radioStationRepository,
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

        return $this->asJson(
            $response,
            [
                'id' => $station->getId(),
                'name' => $station->getName(),
                'url' => $station->getUrl(),
            ]
        );
    }
}
