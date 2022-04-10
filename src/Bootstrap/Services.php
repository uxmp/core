<?php

declare(strict_types=1);

namespace Uxmp\Core\Bootstrap;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;
use getID3;
use Nyholm\Psr7\Factory\Psr17Factory;
use Tzsk\Collage\MakeCollage;
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
            Setup::createAnnotationMetadataConfiguration(
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
];
