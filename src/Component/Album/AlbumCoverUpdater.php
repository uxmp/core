<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Album;

use Uxmp\Core\Orm\Model\AlbumInterface;

final class AlbumCoverUpdater implements AlbumCoverUpdaterInterface
{
    public function update(
        AlbumInterface $album,
        array $metadata
    ): void {
        $destination = __DIR__ . '/../../../../assets/img/album/';

        // search in comments
        $image = $metadata['comments']['picture'][0] ?? null;
        if ($image !== null) {
            $filename = realpath($destination) . '/' . $album->getMbid();
            if (!file_exists($filename)) {
                try {
                    $extension = match ($image['image_mime']) {
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/gif' => 'gif',
                    };
                } catch (\UnhandledMatchError) {
                    // @todo log this
                }

                file_put_contents($filename. '.' . $extension, $image['data']);
            }
        }
    }
}
