<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\AccessKeyInterface;

/**
 * @extends ObjectRepository<AccessKeyInterface>
 *
 * @method null|AccessKeyInterface findOneBy(mixed[] $criteria)
 * @method null|AccessKeyInterface find(int $id)
 */
interface AccessKeyRepositoryInterface extends ObjectRepository
{
    public function prototype(): AccessKeyInterface;

    public function save(AccessKeyInterface $accessKey): void;

    public function delete(AccessKeyInterface $accessKey): void;
}
