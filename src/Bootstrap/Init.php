<?php

declare(strict_types=1);

namespace Usox\Core\Bootstrap;

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use getID3;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Usox\Core\Api\Album\AlbumListApplication;
use Usox\Core\Api\Playback\PlaySongApplication;
use Usox\Core\Component\Catalog\CatalogScanner;
use Usox\Core\Component\Catalog\CatalogScannerInterface;
use Usox\Core\Orm\Model\Album;
use Usox\Core\Orm\Model\Artist;
use Usox\Core\Orm\Model\Song;
use Usox\Core\Orm\Repository\AlbumRepositoryInterface;
use Usox\Core\Orm\Repository\ArtistRepositoryInterface;
use Usox\Core\Orm\Repository\SongRepositoryInterface;
use function DI\autowire;

final class Init
{
    /**
     * @param callable(ContainerInterface): mixed $app
     */
    static function run(callable $app)
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            AlbumListApplication::class => autowire(),
            PlaySongApplication::class => autowire(),
            Psr17Factory::class => autowire(),
            CatalogScannerInterface::class => autowire(CatalogScanner::class),
            getID3::class => autowire(),
            ArtistRepositoryInterface::class => function (ContainerInterface $c): ArtistRepositoryInterface {
                return $c->get(EntityManagerInterface::class)->getRepository(Artist::class);
            },
            AlbumRepositoryInterface::class => function (ContainerInterface $c): AlbumRepositoryInterface {
                return $c->get(EntityManagerInterface::class)->getRepository(Album::class);
            },
            SongRepositoryInterface::class => function (ContainerInterface $c): SongRepositoryInterface {
                return $c->get(EntityManagerInterface::class)->getRepository(Song::class);
            },
            EntityManagerInterface::class => static function (ContainerInterface $c): EntityManagerInterface {
                $paths = [__DIR__ . '/../Orm/Model/'];
                $isDevMode = true;

                // the connection configuration
                $dbParams = [
                    'url' => 'sqlite:///'. __DIR__ .'/../../dev/db.sqlite',
                ];

                $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
                return EntityManager::create($dbParams, $config);
            },
        ]);
        $container = $builder->build();

        return $app($container);
    }
}