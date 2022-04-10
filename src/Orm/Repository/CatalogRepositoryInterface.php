<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\CatalogInterface;

/**
 * @extends ObjectRepository<CatalogInterface>
 *
 * @method null|CatalogInterface findOneBy(mixed[] $criteria)
 */
interface CatalogRepositoryInterface extends ObjectRepository
{
    public function prototype(): CatalogInterface;

    public function save(CatalogInterface $catalog): void;

    public function delete(CatalogInterface $catalog): void;
}
