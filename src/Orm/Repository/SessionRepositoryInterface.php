<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\SessionInterface;

/**
 * @extends EntityRepository<SessionInterface>
 *
 * @method null|SessionInterface findOneBy(mixed[] $criteria)
 * @method null|SessionInterface find(int $id)
 */
interface SessionRepositoryInterface extends ObjectRepository
{
    public function prototype(): SessionInterface;

    public function save(SessionInterface $session): void;

    public function delete(SessionInterface $session): void;
}
