<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\AccessKey;
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

    public function save(AccessKeyInterface $accessKey): void
    {
        $this->getEntityManager()->persist($accessKey);
        $this->getEntityManager()->flush();
    }

    public function delete(AccessKeyInterface $accessKey): void
    {
        $this->getEntityManager()->remove($accessKey);
        $this->getEntityManager()->flush();
    }
}
