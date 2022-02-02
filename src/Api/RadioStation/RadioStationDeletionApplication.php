<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\RadioStation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;

/**
 * Radio station deletion
 */
final class RadioStationDeletionApplication extends AbstractApiApplication
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

        $result = false;

        if ($station !== null) {
            $this->radioStationRepository->delete($station);

            $result = true;
        }

        return $this->asJson(
            $response,
            [
                'result' => $result
            ]
        );
    }
}
