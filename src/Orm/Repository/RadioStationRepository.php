<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Uxmp\Core\Orm\Model\RadioStation;
use Uxmp\Core\Orm\Model\RadioStationInterface;

/**
 * @extends EntityRepository<RadioStationInterface>
 *
 * @method null|RadioStationInterface find(mixed $id)
 */
final class RadioStationRepository extends EntityRepository implements RadioStationRepositoryInterface
{
    /**
     * Create a new RadioStation instance
     */
    public function prototype(): RadioStationInterface
    {
        return new RadioStation();
    }

    /**
     * Saves the RadioStation
     */
    public function save(RadioStationInterface $station): void
    {
        $this->getEntityManager()->persist($station);
        $this->getEntityManager()->flush();
    }

    /**
     * Deletes the RadioStation
     */
    public function delete(RadioStationInterface $station): void
    {
        $this->getEntityManager()->remove($station);
        $this->getEntityManager()->flush();
    }
}
