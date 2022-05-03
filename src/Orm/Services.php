<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Uxmp\Core\Orm\Model\AccessKey;
use Uxmp\Core\Orm\Model\Album;
use Uxmp\Core\Orm\Model\Artist;
use Uxmp\Core\Orm\Model\Catalog;
use Uxmp\Core\Orm\Model\Disc;
use Uxmp\Core\Orm\Model\Favorite;
use Uxmp\Core\Orm\Model\PlaybackHistory;
use Uxmp\Core\Orm\Model\Playlist;
use Uxmp\Core\Orm\Model\RadioStation;
use Uxmp\Core\Orm\Model\Session;
use Uxmp\Core\Orm\Model\Song;
use Uxmp\Core\Orm\Model\TemporaryPlaylist;
use Uxmp\Core\Orm\Model\User;
use Uxmp\Core\Orm\Repository\AccessKeyRepositoryInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;
use Uxmp\Core\Orm\Repository\FavoriteRepositoryInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;
use Uxmp\Core\Orm\Repository\RadioStationRepositoryInterface;
use Uxmp\Core\Orm\Repository\SessionRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepositoryInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

return [
    ArtistRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(Artist::class),
    AlbumRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(Album::class),
    SongRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(Song::class),
    DiscRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(Disc::class),
    SessionRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(Session::class),
    UserRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(User::class),
    CatalogRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(Catalog::class),
    FavoriteRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(Favorite::class),
    PlaybackHistoryRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(PlaybackHistory::class),
    RadioStationRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(RadioStation::class),
    PlaylistRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(Playlist::class),
    AccessKeyRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(AccessKey::class),
    TemporaryPlaylistRepositoryInterface::class => fn (EntityManagerInterface $em) => $em->getRepository(TemporaryPlaylist::class),
];
