<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\Catalog;
use Uxmp\Core\Orm\Model\CatalogInterface;

/**
 * @extends EntityRepository<CatalogInterface>
 *
 * @method null|CatalogInterface findOneBy(mixed[] $criteria)
 * @method null|CatalogInterface find(int $id)
 */
final class CatalogRepository extends EntityRepository implements CatalogRepositoryInterface
{
    #[Pure]
    public function prototype(): CatalogInterface
    {
        return new Catalog();
    }

    public function save(CatalogInterface $catalog): void
    {
        $this->getEntityManager()->persist($catalog);
        $this->getEntityManager()->flush();
    }

    public function delete(CatalogInterface $catalog): void
    {
        $this->getEntityManager()->remove($catalog);
        $this->getEntityManager()->flush();
    }
}
