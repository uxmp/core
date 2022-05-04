<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\RadioStation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;

/**
 * Adds a radio station
 */
final class RadioStationCreationApplication extends AbstractApiApplication
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
        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'RadioStationCreation.json',
        );

        $station = $this->radioStationRepository->prototype()
            ->setName($body['name'])
            ->setUrl($body['url']);

        $this->radioStationRepository->save($station);

        return $this->asJson(
            $response,
            [
                'result' => $station->getId(),
            ]
        );
    }
}
