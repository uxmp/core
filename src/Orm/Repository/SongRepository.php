<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\Favorite;
use Uxmp\Core\Orm\Model\Song;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\UserInterface;

/**
 * @extends EntityRepository<SongInterface>
 *
 * @method null|SongInterface find(mixed $id)
 */
final class SongRepository extends EntityRepository implements SongRepositoryInterface
{
    #[Pure]
    public function prototype(): SongInterface
    {
        return new Song();
    }

    public function save(SongInterface $song): void
    {
        $this->getEntityManager()->persist($song);
        $this->getEntityManager()->flush();
    }

    public function delete(SongInterface $song): void
    {
        $this->getEntityManager()->remove($song);
        $this->getEntityManager()->flush();
    }

    public function findByMbId(string $mbid): ?SongInterface
    {
        return $this->findOneBy([
            'mbid' => $mbid
        ]);
    }

    /**
     * Retrieve a complete list of favorite songs for a user
     *
     * @return iterable<SongInterface>
     */
    public function findFavorites(UserInterface $user): iterable
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder();
        $qbSub = $this
            ->getEntityManager()
            ->createQueryBuilder();
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $and = $expr->andX();
        $and->add($expr->eq('fav.user', ':user'));
        $and->add($expr->eq('fav.type', ':type'));

        $qb
            ->select('a')
            ->from(Song::class, 'a')
            ->where(
                $expr->in(
                    'a.id',
                    $qbSub
                        ->select('fav.item_id')
                        ->from(Favorite::class, 'fav')
                        ->where($and)
                        ->getDQL()
                )
            )
            ->setParameter('user', $user)
            ->setParameter('type', 'song')
        ;

        return $qb->getQuery()->getResult();
    }
}
