<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\DiscInterface;

/**
 * @extends ObjectRepository<DiscInterface>
 */
interface DiscRepositoryInterface extends ObjectRepository
{
    public function prototype(): DiscInterface;

    public function save(DiscInterface $disc): void;

    public function delete(DiscInterface $disc): void;

    /**
     * Find a unique disc by its mbid and the disc number within a release group
     */
    public function findUniqueDisc(
        string $musicBrainzDiscId,
        int $discNumber,
    ): ?DiscInterface;

    /**
     * Searches for discs without songs
     *
     * @return array<DiscInterface>
     */
    public function findEmptyDiscs(CatalogInterface $catalog): array;
}
