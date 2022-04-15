<?php

declare(strict_types=1);

namespace Uxmp\Core\Bootstrap;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;
use getID3;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Tzsk\Collage\MakeCollage;
use Usox\HyperSonic\FeatureSet\V1161\FeatureSetFactory;
use Usox\HyperSonic\HyperSonic;
use Usox\HyperSonic\HyperSonicInterface;
use Uxmp\Core\Component\SubSonic\ArtistListDataProvider;
use Uxmp\Core\Component\SubSonic\AuthenticationProvider;
use Uxmp\Core\Component\SubSonic\LicenseDataProvider;
use Uxmp\Core\Component\SubSonic\PingDataProvider;
use function DI\autowire;

/**
 * This file describes all services provided by external dependencies
 */
return [
    Psr17Factory::class => autowire(),
    getID3::class => autowire(),
    EntityManagerInterface::class => static function (): EntityManagerInterface {
        // set up the EntityManager for the db connection
        return EntityManager::create(
            [
                'url' => $_ENV['DATABASE_DSN'],
                'password' => $_ENV['DATABASE_PASSWORD'],
            ],
            Setup::createAttributeMetadataConfiguration(
                [__DIR__ . '/../Orm/Model/'],
                (bool) ($_ENV['DEBUG_MODE'] ?? false),
            )
        );
    },
    Dotenv::class => function (): Dotenv {
        $dotenv = Dotenv::createImmutable(
            __DIR__ . '/../../',
            ['.env', '.env.dist']
        );
        $dotenv->load();
        return $dotenv;
    },
    MakeCollage::class => autowire(),
    HyperSonicInterface::class => function (ContainerInterface $c): HyperSonicInterface {
        return HyperSonic::init(
            new FeatureSetFactory(),
            $c->get(AuthenticationProvider::class),
            [
                'ping.view' => fn () => $c->get(PingDataProvider::class),
                'getLicense.view' => fn () => $c->get(LicenseDataProvider::class),
                'getArtists.view' => fn () => $c->get(ArtistListDataProvider::class),
            ],
        );
    },
];
