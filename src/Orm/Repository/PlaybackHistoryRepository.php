<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Uxmp\Core\Orm\Model\PlaybackHistory;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Model\SongInterface;

/**
 * @extends EntityRepository<PlaybackHistoryInterface>
 *
 * @method PlaybackHistoryInterface[] findBy(mixed[] $criteria, null|array $order = null, null|int $limit = null, null|int $offset = null)
 */
final class PlaybackHistoryRepository extends EntityRepository implements PlaybackHistoryRepositoryInterface
{
    public function prototype(): PlaybackHistoryInterface
    {
        return new PlaybackHistory();
    }

    public function findBySong(SongInterface $song): iterable
    {
        return $this->findBy(['song' => $song]);
    }

    public function save(PlaybackHistoryInterface $playbackHistory): void
    {
        $em = $this->getEntityManager();

        $em->persist($playbackHistory);
        $em->flush();
    }

    public function delete(PlaybackHistoryInterface $playbackHistory): void
    {
        $em = $this->getEntityManager();

        $em->remove($playbackHistory);
        $em->flush();
    }
}
