<?php

declare(strict_types=1);

namespace Uxmp\Core\Bootstrap;

use Configula\ConfigFactory;
use Configula\ConfigValues;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use getID3;
use Nyholm\Psr7\Factory\Psr17Factory;
use Ramsey\Uuid\Doctrine\UuidType;
use Tzsk\Collage\MakeCollage;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use function DI\autowire;

/**
 * This file describes all services provided by external dependencies
 */
return [
    Psr17Factory::class => autowire(),
    getID3::class => autowire(),
    EntityManagerInterface::class => static function (ConfigProviderInterface $config): EntityManagerInterface {
        // register uuid type
        Type::addType('uuid', UuidType::class);

        return EntityManager::create(
            [
                'url' => $config->getDatabaseDsn(),
                'password' => $config->getDatabasePassword(),
            ],
            ORMSetup::createAttributeMetadataConfiguration(
                [__DIR__ . '/../Orm/Model/'],
                $config->getDebugMode(),
            )
        );
    },
    ConfigValues::class => function (): ConfigValues {
        return ConfigFactory::loadSingleDirectory(__DIR__ . '/../../config');
    },
    MakeCollage::class => autowire(),
];
