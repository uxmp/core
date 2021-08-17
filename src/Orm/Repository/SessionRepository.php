<?php

declare(strict_types=1);

namespace Usox\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Usox\Core\Orm\Model\Session;
use Usox\Core\Orm\Model\SessionInterface;

/**
 * @extends EntityRepository<SessionInterface>
 *
 * @method null|SessionInterface findOneBy(mixed[] $criteria)
 */
final class SessionRepository extends EntityRepository implements SessionRepositoryInterface
{
    public function prototype(): SessionInterface
    {
        return new Session();
    }

    public function save(SessionInterface $session): void
    {
        $this->getEntityManager()->persist($session);
        $this->getEntityManager()->flush();
    }

    public function delete(SessionInterface $session): void
    {
        $this->getEntityManager()->remove($session);
        $this->getEntityManager()->flush();
    }
}
