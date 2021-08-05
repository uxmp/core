<?php

declare(strict_types=1);

namespace Usox\Core\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Usox\Core\Orm\Model\Album;
use Usox\Core\Orm\Model\Artist;
use Usox\Core\Orm\Model\Disc;
use Usox\Core\Orm\Model\Song;
use Usox\Core\Orm\Repository\AlbumRepositoryInterface;
use Usox\Core\Orm\Repository\ArtistRepositoryInterface;
use Usox\Core\Orm\Repository\DiscRepositoryInterface;
use Usox\Core\Orm\Repository\SongRepositoryInterface;

return [
    ArtistRepositoryInterface::class => function (ContainerInterface $c): ArtistRepositoryInterface {
        return $c->get(EntityManagerInterface::class)->getRepository(Artist::class);
    },
    AlbumRepositoryInterface::class => function (ContainerInterface $c): AlbumRepositoryInterface {
        return $c->get(EntityManagerInterface::class)->getRepository(Album::class);
    },
    SongRepositoryInterface::class => function (ContainerInterface $c): SongRepositoryInterface {
        return $c->get(EntityManagerInterface::class)->getRepository(Song::class);
    },
    DiscRepositoryInterface::class => function (ContainerInterface $c): DiscRepositoryInterface {
        return $c->get(EntityManagerInterface::class)->getRepository(Disc::class);
    },
];
