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
    /**
     * Create a new RadioStation instance
     */
    public function prototype(): RadioStationInterface;

    /**
     * Saves the RadioStation
     */
    public function save(RadioStationInterface $station): void;

    /**
     * Deletes the RadioStation
     */
    public function delete(RadioStationInterface $station): void;
}
