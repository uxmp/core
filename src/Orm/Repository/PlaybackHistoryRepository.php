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

    /**
     * @param int $number Amount of items to retrieve
     *
     * @return iterable<array{cnt: int, song_id: int}>
     */
    public function getMostPlayed(int $number = 10): iterable
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(c) as cnt, c.song_id')
            ->from(PlaybackHistory::class, 'c')
            ->groupBy('c.song_id')
            ->orderBy('cnt', 'DESC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult();
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
