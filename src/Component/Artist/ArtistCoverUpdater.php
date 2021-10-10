<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Artist;

use Intervention\Image\Image;
use Tzsk\Collage\MakeCollage;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;

final class ArtistCoverUpdater implements ArtistCoverUpdaterInterface
{
    public function __construct(
        private ConfigProviderInterface $config
    ) {
    }

    public function update(
        ArtistInterface $artist
    ): void {
        $images = [];

        $albumDestination = realpath($this->config->getAssetPath() . '/img/album');
        $artistDestination = realpath($this->config->getAssetPath() . '/img/artist');

        foreach ($artist->getAlbums() as $album) {
            $albumImage = sprintf('%s/%s.jpg', $albumDestination, $album->getMbid());

            if (file_exists($albumImage)) {
                $images[] = $albumImage;
            }
        }

        if ($images === []) {
            return;
        }

        if (count($images) > 4) {
            $images = array_slice($images, 0, 4);
        }

        $collage = new MakeCollage();

        /** @var Image $image */
        $image = $collage
            ->make(600, 600)
            ->padding(10)
            ->background('#000')
            ->from($images);

        $image->save($artistDestination . '/' . $artist->getMbid() . '.jpg');
    }
}
