<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Adds a playlist
 */
final class PlaylistCreationApplication extends AbstractApiApplication
{
    /**
     * @param SchemaValidatorInterface<array{name: string, url: string}> $schemaValidator
     */
    public function __construct(
        private PlaylistRepositoryInterface $playlistRepository,
        private SchemaValidatorInterface $schemaValidator,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'PlaylistCreation.json',
        );

        $playlist = $this->playlistRepository->prototype()
            ->setName($body['name'])
            ->setOwnerUser($request->getAttribute(SessionValidatorMiddleware::USER))
        ;

        $this->playlistRepository->save($playlist);

        return $this->asJson(
            $response->withStatus(StatusCode::CREATED),
            [
                'result' => $playlist->getId(),
            ]
        );
    }
}
