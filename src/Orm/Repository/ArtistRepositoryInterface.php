<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\ArtistInterface;

/**
 * @extends ObjectRepository<ArtistInterface>
 *
 * @method iterable<ArtistInterface> findAll()
 * @method null|ArtistInterface findOneBy()
 */
interface ArtistRepositoryInterface extends ObjectRepository
{
    public function prototype(): ArtistInterface;

    public function save(ArtistInterface $artist): void;

    public function findByMbId(string $mbid): ?ArtistInterface;
}
