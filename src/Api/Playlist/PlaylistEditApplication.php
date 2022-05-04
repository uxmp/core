<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\Component\OwnerValidationTrait;
use Uxmp\Core\Api\Lib\Exception\AccessViolation;
use Uxmp\Core\Api\Lib\Exception\ValidatorException;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Edits a playlist
 */
final class PlaylistEditApplication extends AbstractApiApplication
{
    use OwnerValidationTrait;

    /**
     * @param SchemaValidatorInterface<array{name: string, url: string}> $schemaValidator
     */
    public function __construct(
        private readonly PlaylistRepositoryInterface $playlistRepository,
        private readonly SchemaValidatorInterface $schemaValidator,
    ) {
    }

    /**
     * @throws ValidatorException
     * @throws AccessViolation
     */
    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $playlistId = (int) ($args['playlistId'] ?? 0);

        $playlist = $this->playlistRepository->find($playlistId);
        if ($playlist === null) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        $this->validateOwner($request, $playlist);

        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'PlaylistCreation.json',
        );

        $playlist
            ->setName($body['name']);

        $this->playlistRepository->save($playlist);

        return $this->asJson(
            $response,
            [
                'result' => true,
            ]
        );
    }
}
