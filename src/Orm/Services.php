<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Orm\Model\Album;
use Uxmp\Core\Orm\Model\Artist;
use Uxmp\Core\Orm\Model\Catalog;
use Uxmp\Core\Orm\Model\Disc;
use Uxmp\Core\Orm\Model\Session;
use Uxmp\Core\Orm\Model\Song;
use Uxmp\Core\Orm\Model\User;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;
use Uxmp\Core\Orm\Repository\SessionRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

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
    SessionRepositoryInterface::class => function (ContainerInterface $c): SessionRepositoryInterface {
        return $c->get(EntityManagerInterface::class)->getRepository(Session::class);
    },
    UserRepositoryInterface::class => function (ContainerInterface $c): UserRepositoryInterface {
        return $c->get(EntityManagerInterface::class)->getRepository(User::class);
    },
    CatalogRepositoryInterface::class => function (ContainerInterface $c): CatalogRepositoryInterface {
        return $c->get(EntityManagerInterface::class)->getRepository(Catalog::class);
    },
];
