<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Generator;
use Usox\HyperSonic\FeatureSet\V1161\Contract\MusicFolderListDataProviderInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;

/**
 * Retrieves the list of available catalogs
 */
final class MusicFoldersDataProvider implements MusicFolderListDataProviderInterface
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalogRepository,
    ) {
    }

    /**
     * @return Generator<array{
     *  id: string,
     *  name: string,
     * }>
     */
    public function getMusicFolders(): Generator
    {
        $catalogs = $this->catalogRepository->findAll();

        foreach ($catalogs as $catalog) {
            yield [
                'id' => (string) $catalog->getId(),
                'name' => 'Catalog',
            ];
        }
    }
}
