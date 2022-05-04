<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Artist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

final class ArtistSongsApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly AlbumRepositoryInterface $albumRepository,
        private readonly ResultItemFactoryInterface $resultItemFactory
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $list = [];

        $artistId = (int) ($args['artistId'] ?? 0);

        $result = $this->albumRepository->findBy(['artist_id' => $artistId], ['title' => 'ASC']);

        foreach ($result as $album) {
            foreach ($album->getDiscs() as $disc) {
                foreach ($disc->getSongs() as $song) {
                    $list[] = $this->resultItemFactory->createSongListItem($song, $album);
                }
            }
        }

        return $this->asJson($response, ['items' => $list]);
    }
}
