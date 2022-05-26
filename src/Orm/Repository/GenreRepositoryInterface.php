<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\GenreInterface;

/**
 * @extends ObjectRepository<GenreInterface>
 *
 * @method null|GenreInterface findOneBy(mixed[] $criteria)
 */
interface GenreRepositoryInterface extends ObjectRepository
{
    public function prototype(): GenreInterface;

    public function save(GenreInterface $genre): void;

    public function delete(GenreInterface $genre): void;
}