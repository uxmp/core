<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Album;

use Uxmp\Core\Component\Artist\ArtistCoverUpdaterInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;

final class AlbumCoverUpdater implements AlbumCoverUpdaterInterface
{
    public function __construct(
        private ConfigProviderInterface $config,
        private ArtistCoverUpdaterInterface $artistCoverUpdater
    ) {
    }

    public function update(
        AlbumInterface $album,
        array $metadata
    ): void {
        $destination = realpath($this->config->getAssetPath() . '/img/album');

        // search in comments
        /** @var null|array{image_mime: string, data: string} $image */
        $image = $metadata['comments']['picture'][0] ?? null;
        if ($image !== null) {
            $filename = $destination . '/' . $album->getMbid();
            if (!file_exists($filename)) {
                $extension = match ($image['image_mime']) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    default => null,
                };

                if ($extension === null) {
                    return;
                }

                file_put_contents($filename. '.' . $extension, $image['data']);

                $this->artistCoverUpdater->update($album->getArtist());
            }
        }
    }
}
