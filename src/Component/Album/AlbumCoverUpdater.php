<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Album;

use UnhandledMatchError;
use Uxmp\Core\Orm\Model\AlbumInterface;

final class AlbumCoverUpdater implements AlbumCoverUpdaterInterface
{
    public function update(
        AlbumInterface $album,
        array $metadata
    ): void {
        $destination = __DIR__ . '/../../../../assets/img/album/';

        // search in comments
        /** @var null|array{image_mime: string, data: string} $image */
        $image = $metadata['comments']['picture'][0] ?? null;
        if ($image !== null) {
            $filename = realpath($destination) . '/' . $album->getMbid();
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
            }
        }
    }
}
