<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Artist;

use DateTime;
use Intervention\Image\Image;
use Tzsk\Collage\MakeCollage;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

final class ArtistCoverUpdater implements ArtistCoverUpdaterInterface
{
    public function __construct(
        private readonly ConfigProviderInterface $config,
        private readonly ArtistRepositoryInterface $artistRepository,
        private readonly MakeCollage $collageMaker,
    ) {
    }

    public function update(
        ArtistInterface $artist
    ): void {
        $images = [];
        $assetPath = $this->config->getAssetPath();

        $albumDestination = sprintf(
            '%s/img/album',
            $assetPath
        );

        foreach ($artist->getAlbums() as $album) {
            $albumImage = sprintf('%s/%s.jpg', $albumDestination, $album->getMbid());

            if (file_exists($albumImage)) {
                $images[] = $albumImage;
            }
        }

        if ($images === []) {
            return;
        }

        // MakeCollage only support up to 4 images, to take the first ones
        if (count($images) > 4) {
            $images = array_slice($images, 0, 4);
        }

        /** @var Image $image */
        $image = $this->collageMaker
            ->make(600, 600)
            ->padding(10)
            ->background('#000')
            ->from($images);

        $artistDestination = sprintf(
            '%s/img/artist',
            $assetPath
        );

        $image->save(
            sprintf(
                '%s/%s.jpg',
                $artistDestination,
                $artist->getMbid()
            )
        );

        $artist->setLastModified(new DateTime());

        $this->artistRepository->save($artist);
    }
}
