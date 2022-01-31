<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\RadioStation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;

/**
 * Adds a radio station
 */
final class RadioStationCreationApplication extends AbstractApiApplication
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
        /** @var array<string, mixed> $body */
        $body = $request->getParsedBody();

        // @todo add validation checks
        $name = (string) ($body['name'] ?? '');
        $url = (string) ($body['url'] ?? '');

        $station = $this->radioStationRepository->prototype()
            ->setName($name)
            ->setUrl($url);

        $this->radioStationRepository->save($station);

        return $this->asJson(
            $response,
            [
                'result' => $station->getId()
            ]
        );
    }
}
