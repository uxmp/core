<?php

declare(strict_types=1);

namespace Uxmp\Core\Bootstrap;

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;
use getID3;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Tzsk\Collage\MakeCollage;
use Uxmp\Core\Component\Event\EventHandlerInterface;
use function DI\autowire;

final class Init
{
    /**
     * @param callable(ContainerInterface):mixed $app
     */
    public static function run(callable $app): mixed
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(require __DIR__ . '/../Api/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Album/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Catalog/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Event/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Config/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Session/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Song/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Disc/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Artist/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Art/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Setup/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Tag/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Favorite/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Cli/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/User/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Orm/Services.php');
        $builder->addDefinitions([
            Psr17Factory::class => autowire(),
            getID3::class => autowire(),
            EntityManagerInterface::class => static function (): EntityManagerInterface {
                $paths = [__DIR__ . '/../Orm/Model/'];

                // @todo load from config
                $isDevMode = true;

                // the connection configuration
                $dbParams = [
                    'url' => $_ENV['DATABASE_DSN'],
                    'password' => $_ENV['DATABASE_PASSWORD'],
                ];

                $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
                return EntityManager::create($dbParams, $config);
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
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->build();

        // @todo validate env variable
        $container->get(Dotenv::class);

        $result = $app($container);

        $container->get(EventHandlerInterface::class)->run();

        return $result;
    }
}
