<?php

namespace Usox\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Usox\Core\Orm\Model\DiscInterface;

/**
 * @extends ObjectRepository<DiscInterface>
 */
interface DiscRepositoryInterface extends ObjectRepository
{
    public function prototype(): DiscInterface;

    public function save(DiscInterface $disc): void;
}
