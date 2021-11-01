<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Album;

use Uxmp\Core\Component\Artist\ArtistCoverUpdaterInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumDeleter implements AlbumDeleterInterface
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository,
        private ConfigProviderInterface $config,
        private ArtistCoverUpdaterInterface $artistCoverUpdater,
    ) {
    }

    public function delete(AlbumInterface $album): void
    {
        $path = sprintf(
            '%s/img/%s/%s.jpg',
            $this->config->getAssetPath(),
            $album->getArtItemType(),
            $album->getArtItemId()
        );

        if (file_exists($path)) {
            unlink($path);
        }

        $artist = $album->getArtist();

        $this->albumRepository->delete($album);

        $this->artistCoverUpdater->update($artist);
    }
}
