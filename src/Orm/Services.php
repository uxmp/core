<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Orm\Model\Album;
use Uxmp\Core\Orm\Model\Artist;
use Uxmp\Core\Orm\Model\Catalog;
use Uxmp\Core\Orm\Model\Disc;
use Uxmp\Core\Orm\Model\Favorite;
use Uxmp\Core\Orm\Model\PlaybackHistory;
use Uxmp\Core\Orm\Model\RadioStation;
use Uxmp\Core\Orm\Model\Session;
use Uxmp\Core\Orm\Model\Song;
use Uxmp\Core\Orm\Model\User;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;
use Uxmp\Core\Orm\Repository\FavoriteRepositoryInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;
use Uxmp\Core\Orm\Repository\SessionRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

return [
    ArtistRepositoryInterface::class => fn (ContainerInterface $c): ArtistRepositoryInterface =>
        $c->get(EntityManagerInterface::class)->getRepository(Artist::class),
    AlbumRepositoryInterface::class => fn (ContainerInterface $c): AlbumRepositoryInterface =>
        $c->get(EntityManagerInterface::class)->getRepository(Album::class),
    SongRepositoryInterface::class => fn (ContainerInterface $c): SongRepositoryInterface =>
        $c->get(EntityManagerInterface::class)->getRepository(Song::class),
    DiscRepositoryInterface::class => fn (ContainerInterface $c): DiscRepositoryInterface =>
        $c->get(EntityManagerInterface::class)->getRepository(Disc::class),
    SessionRepositoryInterface::class => fn (ContainerInterface $c): SessionRepositoryInterface =>
        $c->get(EntityManagerInterface::class)->getRepository(Session::class),
    UserRepositoryInterface::class => fn (ContainerInterface $c): UserRepositoryInterface =>
        $c->get(EntityManagerInterface::class)->getRepository(User::class),
    CatalogRepositoryInterface::class => fn (ContainerInterface $c): CatalogRepositoryInterface =>
        $c->get(EntityManagerInterface::class)->getRepository(Catalog::class),
    FavoriteRepositoryInterface::class => fn (ContainerInterface $c): FavoriteRepositoryInterface =>
        $c->get(EntityManagerInterface::class)->getRepository(Favorite::class),
    PlaybackHistoryRepositoryInterface::class => fn (ContainerInterface $c): PlaybackHistoryRepositoryInterface =>
        $c->get(EntityManagerInterface::class)->getRepository(PlaybackHistory::class),
    RadioStationRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(RadioStation::class),
];
