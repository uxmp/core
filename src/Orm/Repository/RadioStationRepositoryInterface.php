<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\RadioStationInterface;

/**
 * @extends ObjectRepository<RadioStationInterface>
 *
 * @method null|RadioStationInterface find(mixed $id)
 */
interface RadioStationRepositoryInterface extends ObjectRepository
{
    public function prototype(): RadioStationInterface;

    public function save(RadioStationInterface $station): void;
}
