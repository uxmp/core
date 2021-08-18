<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Uxmp\Core\Orm\Model\User;
use Uxmp\Core\Orm\Model\UserInterface;

/**
 * @extends EntityRepository<UserInterface>
 *
 * @method null|UserInterface findOneBy(mixed[] $criteria)
 */
final class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    public function prototype(): UserInterface
    {
        return new User();
    }

    public function save(UserInterface $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function delete(UserInterface $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}
