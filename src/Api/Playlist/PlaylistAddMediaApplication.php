<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\Component\OwnerValidationTrait;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Component\Playlist\MediaAddition\PlaylistMediaAdderInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Adds songs of a media to a playlist
 */
final class PlaylistAddMediaApplication extends AbstractApiApplication
{
    use OwnerValidationTrait;

    /**
     * @param SchemaValidatorInterface<array{mediaType: string, mediaId: int}> $schemaValidator
     */
    public function __construct(
        private readonly PlaylistRepositoryInterface $playlistRepository,
        private readonly SchemaValidatorInterface $schemaValidator,
        private readonly PlaylistMediaAdderInterface $playlistMediaAdder,
    ) {
    }

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

        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'PlaylistMediaAddition.json',
        );

        $this->validateOwner($request, $playlist);

        $this->playlistMediaAdder->add(
            $playlist,
            $body['mediaType'],
            $body['mediaId'],
        );

        return $this->asJson(
            $response,
            [
                'result' => true,
            ]
        );
    }
}
