<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\RadioStation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Model\RadioStationInterface;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;

/**
 * Radio station list
 */
final class RadioStationListApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly RadioStationRepositoryInterface $radioStationRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $stations = $this->radioStationRepository->findBy([], ['name' => 'ASC']);

        $result = array_map(
            static fn (RadioStationInterface $station): array => [
                'id' => $station->getId(),
                'name' => $station->getName(),
                'url' => $station->getUrl(),
            ],
            $stations
        );

        return $this->asJson(
            $response,
            [
                'items' => $result,
            ]
        );
    }
}
