<?php

namespace Usox\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Usox\Core\Orm\Model\UserInterface;

/**
 * @extends EntityRepository<UserInterface>
 *
 * @method null|UserInterface findOneBy(mixed[] $criteria)
 */
interface UserRepositoryInterface extends ObjectRepository
{
    public function prototype(): UserInterface;

    public function save(UserInterface $user): void;

    public function delete(UserInterface $user): void;
}