<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Component\Playlist\PlaylistTypeEnum;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;
use ValueError;

/**
 * Creates a playlist
 */
final class PlaylistCreationApplication extends AbstractApiApplication
{
    /**
     * @param SchemaValidatorInterface<array{
     *  name: string,
     *  url: string,
     *  typeId: integer
     * }> $schemaValidator
     */
    public function __construct(
        private readonly PlaylistRepositoryInterface $playlistRepository,
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
            'PlaylistCreation.json',
        );

        try {
            $type = PlaylistTypeEnum::from($body['typeId']);
        } catch (ValueError) {
            return $response->withStatus(StatusCode::BAD_REQUEST);
        }

        $playlist = $this->playlistRepository->prototype()
            ->setName($body['name'])
            ->setOwner($request->getAttribute(SessionValidatorMiddleware::USER))
            ->setType($type)
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
