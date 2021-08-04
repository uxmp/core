<?php

declare(strict_types=1);

namespace Usox\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Usox\Core\Orm\Model\Disc;
use Usox\Core\Orm\Model\DiscInterface;

/**
 * @extends EntityRepository<DiscInterface>
 */
final class DiscRepository extends EntityRepository implements DiscRepositoryInterface
{
    public function prototype(): DiscInterface
    {
        return new Disc();
    }

    public function save(DiscInterface $disc): void
    {
        $this->getEntityManager()->persist($disc);
        $this->getEntityManager()->flush();
    }
}
