<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Component\Favorite\FavoriteAbleInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

abstract class AbstractFavoriteApplication extends AbstractApiApplication
{
    /**
     * @param SchemaValidatorInterface<array{itemId: int}> $schemaValidator
     */
    protected function __construct(
        private readonly SongRepositoryInterface $songRepository,
        private readonly AlbumRepositoryInterface $albumRepository,
        private readonly SchemaValidatorInterface $schemaValidator,
    ) {
    }

    /**
     * @param array<string, scalar> $args
     */
    protected function getItem(
        ServerRequestInterface $request,
        array $args
    ): ?FavoriteAbleInterface {
        /** @var array<string, mixed> $body */
        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'AddRemoveFavoriteItem.json'
        );

        return match ((string) ($args['type'] ?? '')) {
            'song' => $this->songRepository->find($body['itemId']),
            'album' => $this->albumRepository->find($body['itemId']),
            default => null,
        };
    }
}
