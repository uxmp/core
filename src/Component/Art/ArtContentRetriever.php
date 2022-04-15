<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use Uxmp\Core\Component\Config\ConfigProviderInterface;

/**
 * Retrieves the binary data of an art item
 */
final class ArtContentRetriever implements ArtContentRetrieverInterface
{
    public function __construct(
        private ConfigProviderInterface $config,
    ) {
    }

    /**
     * @return array{
     *  mimeType: string,
     *  content: string
     * }
     *
     * @throws Exception\ArtContentException
     */
    public function retrieve(
        CachableArtItemInterface $item,
    ): array {
        $artItemId = $item->getArtItemId();

        if ($artItemId === null) {
            throw new Exception\ArtContentException('item does not provide art');
        }
        $path = sprintf(
            '%s/img/%s/%s.jpg',
            $this->config->getAssetPath(),
            $item->getArtItemType(),
            $artItemId
        );

        // @todo support other mimetypes
        $mimeType = 'image/jpg';

        if (!file_exists($path)) {
            throw new Exception\ArtContentException(
                sprintf('File `%s` not found', $path)
            );
        }

        return [
            'mimeType' => $mimeType,
            'content' => (string) file_get_contents($path),
        ];
    }
}
