<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Component\Playlist\Smartlist\Type\SmartlistTypeInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

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
     * @param array<int, SmartlistTypeInterface> $playlistTypeList
     */
    public function __construct(
        private readonly PlaylistRepositoryInterface $playlistRepository,
        private readonly SchemaValidatorInterface $schemaValidator,
        private readonly array $playlistTypeList,
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

        $typeId = $body['typeId'];

        if (!array_key_exists($typeId, $this->playlistTypeList)) {
            return $response->withStatus(StatusCode::BAD_REQUEST);
        }

        $playlist = $this->playlistRepository->prototype()
            ->setName($body['name'])
            ->setOwner($request->getAttribute(SessionValidatorMiddleware::USER))
            ->setType($typeId)
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
