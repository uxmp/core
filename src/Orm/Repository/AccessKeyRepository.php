<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\AccessKey;
use Uxmp\Core\Orm\Model\Session;
use Uxmp\Core\Orm\Model\AccessKeyInterface;

/**
 * @extends EntityRepository<AccessKeyInterface>
 *
 * @method null|AccessKeyInterface findOneBy(mixed[] $criteria)
 */
final class AccessKeyRepository extends EntityRepository implements AccessKeyRepositoryInterface
{
    #[Pure]
    public function prototype(): AccessKeyInterface
    {
        return new AccessKey();
    }

    public function save(AccessKeyInterface $session): void
    {
        $this->getEntityManager()->persist($session);
        $this->getEntityManager()->flush();
    }

    public function delete(AccessKeyInterface $session): void
    {
        $this->getEntityManager()->remove($session);
        $this->getEntityManager()->flush();
    }
}
